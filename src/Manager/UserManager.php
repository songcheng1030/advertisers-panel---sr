<?php

namespace App\Manager;

use App\Entity\Agency;
use App\Entity\User;
use App\Form\RoleType;
use App\Repository\AgencyRepository;
use App\Repository\CampaignRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Gaufrette\Extras\Resolvable\ResolvableFilesystem;
use Gaufrette\Extras\Resolvable\Resolver\AwsS3PublicUrlResolver;
use Gaufrette\Extras\Resolvable\Resolver\StaticUrlResolver;
use Gaufrette\Extras\Resolvable\UnresolvableObjectException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager extends AbstractManager
{
    const SALES_MANAGER_ROLES = [
        RoleType::ROLE_ADMIN,
        RoleType::ROLE_SALES_MANAGER,
        RoleType::ROLE_SALES_MANAGER_HEAD,
    ];

    /**
     * @var AgencyRepository
     */
    private $agencyRepository;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * UserManager constructor.
     */
    public function __construct(
        ContainerInterface $container,
        ParameterBagInterface $parameters,
        AgencyRepository $agencyRepository,
        CampaignRepository $campaignRepository
    ) {
        parent::__construct($container, $parameters);
        $this->agencyRepository = $agencyRepository;
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @return array
     */
    public function getAdminGroupUsers(?UserInterface $loggedUser)
    {
        $adminGroupUsers = [];
        if ($loggedUser->isAdmin()) {
            $adminGroupUsers = $this
                ->em
                ->getRepository(User::class)
                ->getAdminGroupUsers();
        }

        if ($loggedUser->isSalesManagerHead()) {
            $adminGroupUsers = $this
                ->em
                ->getRepository(User::class)
                ->getSalesManagersForSalesManagerHead($loggedUser);
        }

        return $adminGroupUsers;
    }

    /**
     * @return mixed
     */
    public function getSalesManagerHeads()
    {
        return $this->em->getRepository(User::class)->findByRoles([RoleType::ROLE_SALES_MANAGER_HEAD]);
    }

    /**
     * @return string
     */
    public function getNewUserValidationGroup(?UserInterface $loggedUser, array $selectedRole)
    {
        $validationGroup = 'account-creation';
        if ($loggedUser->isSalesManagerHead()) {
            $validationGroup = 'account-creation-manager-head';
        }

        return $validationGroup;
    }

    /**
     * @throws Exception
     */
    public function saveUserManager(FormInterface $form, ?UserInterface $createdBy)
    {
        $createdAt = new DateTime('now', new DateTimeZone('US/Eastern'));
        $user = $form->getData();

        if ($createdBy->isAdmin()) {
            $user->setRoles($form->get('roles')->getData());
        } else {
            $user->setRoles([RoleType::ROLE_SALES_MANAGER]);
            $user->setSalesManagerHead($createdBy);
        }

        $user->setCreatedBy($createdBy);
        $user->setCreatedAt($createdAt);
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setPassword($this->password_encoder->encodePassword(
            $user,
            $form->get('plainPassword')->getData()
        ));

        $this->em->persist($user);
        $this->em->flush();
    }

    public function getEditAccountValidationGroup(?UserInterface $loggedUser, array $selectedRole)
    {
        $validationGroup = 'account-edition';

        if ($loggedUser->isAdmin()) {
            if (in_array(
                RoleType::ROLE_SALES_MANAGER,
                $selectedRole
            ) || in_array(RoleType::ROLE_SALES_MANAGER_HEAD, $selectedRole)) {
                $validationGroup = 'account-edition-managers';
            }
        }

        return $validationGroup;
    }

    /**
     * @throws Exception
     */
    public function updateUserManager(FormInterface $form, ?UserInterface $updatedBy)
    {
        $updatedAt = new DateTime('now', new DateTimeZone('US/Eastern'));
        $user = $form->getData();
        $password = $form->get('plainPassword')->getData();

        $user->setUpdatedBy($updatedBy);
        $user->setUpdatedAt($updatedAt);

        if ($updatedBy->isAdmin()) {
            $user->setRoles($form->get('roles')->getData());
        }

        if ($password) {
            $user->setPassword($this->password_encoder->encodePassword(
                $user,
                $password
            ));
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @throws Exception
     */
    public function updateAccount(FormInterface $form, ?UserInterface $updatedBy)
    {
        $updatedAt = new DateTime('now', new DateTimeZone('US/Eastern'));
        $user = $form->getData();
        $password = $form->get('plainPassword')->getData();

        $user->setUpdatedBy($updatedBy);
        $user->setUpdatedAt($updatedAt);

        if ($password) {
            $user->setPassword($this->password_encoder->encodePassword(
                $user,
                $password
            ));
        }

        $picture = $form->get('picture')->getData();

        if ($picture && false !== strpos($picture, 'base64')) {
            $user = $this->updateProfilePicture($picture, $user);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @throws UnresolvableObjectException
     */
    public function updateProfilePicture(string $data, User $user, bool $alreadyParsed = false): User
    {
        if (!$alreadyParsed) {
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
        }

        $imageData = base64_decode($data);
        $imageInfo = getimagesizefromstring($imageData);
        $extension = image_type_to_extension($imageInfo[2]);
        $pictureFilename = $user->getId() . $extension;

        $filesystem = $this->getProfilePictureFilesystem();

        if ($filesystem->has($pictureFilename)) {
            $filesystem->delete($pictureFilename);
        }
        $filesystem->write($pictureFilename, $imageData);
        $pictureAsset = $filesystem->resolve($pictureFilename);

        $user->setPicture($pictureAsset);

        return $user;
    }

    private function getProfilePictureFilesystem(): ResolvableFilesystem
    {
        if ('dev' === $this->container->get('kernel')->getEnvironment()) {
            $resolver = new StaticUrlResolver(
                'http://localhost:8000/uploads/profile/'
            );
        } else {
            $resolver = new AwsS3PublicUrlResolver(
                $this->container->get('ct_file_store.s3'),
                $this->parameters->get('uploads_s3_bucket'),
                '/profile'
            );
        }

        $nativeFS = $this->container->get('knp_gaufrette.filesystem_map')->get('pictures_fs');

        return new ResolvableFilesystem(
            $nativeFS,
            $resolver
        );
    }

    public function getSalesManagerById($salesManagerId)
    {
        return $this->em->getRepository(User::class)->find($salesManagerId);
    }

    /**
     * @return array
     */
    public function getAccountManagerFormData(FormInterface $form)
    {
        $ret = [];

        $accountManagerName = $form->get('accountManagerName')->getData();
        $accountManagerEmail = $form->get('accountManagerEmail')->getData();
        $accountManagerPhone = $form->get('accountManagerPhone')->getData();

        if (!is_null($accountManagerName) && !is_null($accountManagerEmail) && !is_null($accountManagerPhone)) {
            $accountManager = [
                'name' => $accountManagerName,
                'email' => $accountManagerEmail,
                'phone' => $accountManagerPhone,
            ];

            $ret[] = $accountManager;
        }

        foreach ($form->get('account_manager')->getData() as $key => $manager) {
            $ret[] = $manager;
        }

        return $ret;
    }

    /**
     * @return Agency[]|array|mixed
     */
    public function getUserAgencies(?UserInterface $loggedUser)
    {
        $agencies = [];
        if ($loggedUser->isAdmin()) {
            $agencies = $this->agencyRepository->findBy(['deleted' => false]);
        } elseif ($loggedUser->isSalesManager()) {
            $agencies = $this->agencyRepository->findby(['deleted' => false, 'salesManager' => $loggedUser->getId()]);
        } elseif ($loggedUser->isSalesManagerHead()) {
            $agencies = $this->agencyRepository->findAgenciesForSalesManagerHead($loggedUser->getId());
        }

        return $agencies;
    }

    public function getReportsUserSalesManager(?UserInterface $loggedUser)
    {
        $salesManagers = [];

        $repo = $this->em->getRepository(User::class);

        if ($loggedUser->isAdmin()) {
            $salesManagers = $repo->findSalesManagersForReportsAdmin($loggedUser->getId());
        } elseif ($loggedUser->isSalesManager()) {
            // un sales manager normal no debería ni haber llegado aqui
        } elseif ($loggedUser->isSalesManagerHead()) {
            $salesManagers = $repo->findSalesManagersForReportsSalesManagerHead($loggedUser->getId());
        }

        return $salesManagers;
    }

    public function getReportsUserAgencies(?UserInterface $loggedUser)
    {
        $agencies = [];
        if ($loggedUser->isAdmin()) {
            $agencies = $this->agencyRepository->findAgenciesForReportsAdmin($loggedUser->getId());
        } elseif ($loggedUser->isSalesManager()) {
            $agencies = $this->agencyRepository->findAgenciesForReportsSalesManager($loggedUser->getId());
        } elseif ($loggedUser->isSalesManagerHead()) {
            $agencies = $this->agencyRepository->findAgenciesForSalesManagerHead($loggedUser->getId());
        }

        return $agencies;
    }

    public function getReportsUserCampaigns(?UserInterface $loggedUser, $requestedField)
    {
        $campaigns = [];
        if ($loggedUser->isAdmin()) {
            $campaigns = $this->campaignRepository->findCampaignsForReportsAdmin($loggedUser->getId(), $requestedField);
        } elseif ($loggedUser->isSalesManager()) {
            $campaigns = $this->campaignRepository->findCampaignsForReportsSalesManager($loggedUser->getId(), $requestedField);
        } elseif ($loggedUser->isSalesManagerHead()) {
            $campaigns = $this->campaignRepository->findCampaignsForReportsSalesManagerHead($loggedUser->getId(), $requestedField);
        } elseif ($loggedUser->isAdvertiser()) {
            $campaigns = $this->campaignRepository->findCampaignsForReportsAdvertisers($loggedUser->getId(), $requestedField);
        } elseif ($loggedUser->isCampaignViewer()) {
            // $campaigns = $this->campaignRepository->findCampaignsForReportsAdmin($loggedUser->getId(), $requestedField);
            $campaigns = $this->campaignRepository->findCampaignsForReportsCampaignViewer($loggedUser->getId(), $requestedField);
        }

        return $campaigns;
    }

    /**
     * @return array
     */
    public function getUserCampaigns(?UserInterface $loggedUser)
    {
        $campaigns = [];
        if ($loggedUser->isAdmin()) {
            $campaigns = $this->campaignRepository->findForAdmin();
        } elseif ($loggedUser->isSalesManager()) {
            $campaigns = $this->campaignRepository->findForSalesManager($loggedUser->getId());
        } elseif ($loggedUser->isSalesManagerHead()) {
            $campaigns = $this->campaignRepository->findForSalesManagerHead($loggedUser->getId());
        }

        return $campaigns;
    }

    public function getSalesManagers()
    {
        return $this->em->getRepository(User::class)->findByRoles(self::SALES_MANAGER_ROLES);
    }

    /**
     * @param $loggedUser
     */
    public function setUserData(User $user, FormInterface $form, $loggedUser, DateTime $createdAt): void
    {
        $password = $form->get('plainPassword')->getData();
        $user
            ->setEmail($form->get('email')->getData())
            ->setUsername($form->get('username')->getData())
            ->setLocale($form->get('locale')->getData())
            ->setCreatedBy($loggedUser)
            ->setCreatedAt($createdAt)
            ->setStatus(User::STATUS_ACTIVE)
            ->setRoles(['ROLE_ADVERTISER'])
            ->setPlainPassword($password);

        if ($password) {
            $user->setPassword($this->password_encoder->encodePassword(
                $user,
                $password
            ));
        }
    }

    /**
     * @param $requestPlainPassword
     */
    public function getUserGroupValidation(bool $hasUser, Request $request): string
    {
        $requestPlainPassword = $request->request->get('advertiser')['plainPassword'];
        $userGroupValidation = 'advertiser-user';

        if ($hasUser) {
            $userGroupValidation = 'advertiser-user-edit';

            if (!empty($requestPlainPassword['first']) || !empty($requestPlainPassword['second'])) {
                $userGroupValidation = 'advertiser-user-edit-password';
            }
        }

        return $userGroupValidation;
    }
}
