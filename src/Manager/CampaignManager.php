<?php

namespace App\Manager;

use App\Entity\Campaign;
use App\Form\CampaignFieldType;
use App\Form\CostFieldType;
use App\Repository\CampaignRepository;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CampaignManager extends AbstractManager
{
    const ALLOWED_MIME_TYPES = ['text/plain', 'text/csv', 'application/vnd.ms-excel'];
    const ALLOWED_CHARACTERS_PATTERN = "/[^a-zA-Z0-9\.\-\n]/";
    const VALIDATION_GROUP_REGISTER = 'register';
    const VALIDATION_GROUP_REGISTER_LKQD = 'register-lkqd';
    const VALIDATION_GROUP_REGISTER_DEAL = 'register-deal';
    const VALIDATION_GROUP_REGISTER_CPV = 'register-cpv';
    const VALIDATION_GROUP_EDIT = 'edit';
    const VALIDATION_GROUP_EDIT_CPV = 'edit-cpv';
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * CampaignManager constructor.
     */
    public function __construct(
        ContainerInterface $container,
        ParameterBagInterface $parameters,
        CampaignRepository $campaignRepository
    ) {
        parent::__construct($container, $parameters);
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @return bool
     */
    public function setListFiles(FormInterface $form, Campaign $campaign)
    {
        $hasError = false;
        $listFiles = $form->get('list')->getData();
        $list = [];

        foreach ($listFiles as $file) {
            if (!in_array($file->getClientMimeType(), self::ALLOWED_MIME_TYPES)) {
                $form
                    ->get('list')
                    ->addError(
                        new FormError($this->translator->trans('common.please_upload_valid_files', [], 'validators'))
                    );
                $hasError = true;
            } else {
                $fileContent = file_get_contents($file->getPathname());
                $cleanLines = preg_replace(self::ALLOWED_CHARACTERS_PATTERN, '', $fileContent);
                $data = explode(PHP_EOL, $cleanLines);
                $list = array_merge($list, $data);
            }
        }
        $campaign->setList($list);

        return $hasError;
    }

    /**
     * @return bool
     */
    public function checkDealId(FormInterface $form, Campaign $campaign)
    {
        $hasError = false;
        $campaigns = $this->campaignRepository->findBy([
            'ssp' => $campaign->getSsp(),
            'dealId' => $campaign->getDealId(),
            'deleted' => false,
        ]);

        if (0 !== count($campaigns)) {
            $form
                ->get('dealId')
                ->addError(
                    new FormError($this->translator->trans('common.must_be_unique_by_ssp', [], 'validators'))
                );
            $hasError = true;
        }

        return $hasError;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function checkDates(FormInterface $form, Campaign $campaign)
    {
        $now = new DateTime('now');
        $now->setTime(0, 0, 0);
        $compareToStartDate = false;

        if (null != $campaign->getId()) {
            $campaign_original = $this->em->createQueryBuilder('aux')
            ->select('c.startAt')
            ->from('App:Campaign', 'c')
            ->where('c.id = :id')
            ->setParameters([
                'id' => $campaign->getId(),
                ])
            ->getQuery()
            ->getResult();
            if (@$campaign_original[0]['startAt']) {
                $now = $campaign_original[0]['startAt'];
            }
        }

        $startAt = $campaign->getStartAt();
        $startDate = $now;
        $endDate = $campaign->getEndAt();

        if ($startAt) {
            $compareToStartDate = true;
            $startDate = $startAt;
        }

        $isStartAtNotValid = $this->checkValidDate($form, $startDate, $now, 'startAt');
        $isEndAtNotValid = $this->checkValidDate($form, $endDate, $startDate, 'endAt', $compareToStartDate);

        return $isStartAtNotValid || $isEndAtNotValid;
    }

    protected function checkValidDate(
        FormInterface $form,
        ?DateTimeInterface $date,
        DateTime $now,
        string $propertyName,
        bool $compareToStartDate = false
    ): bool {
        $hasError = false;

        $errorMessageKey = 'common.date_must_be_after_the_current_date';

        if ($compareToStartDate) {
            $errorMessageKey = 'common.date_must_be_after_start_date';
        }

        if ($date && $date < $now) {
            $form
                ->get($propertyName)
                ->addError(
                    new FormError($this->translator->trans(
                        $errorMessageKey,
                        [],
                        'validators'
                    ))
                );
            $hasError = true;
        }

        return $hasError;
    }

    /**
     * @return Campaign
     *
     * @throws Exception
     */
    public function getCampaign(Request $request, Campaign $selectedCampaign)
    {
        if ('campaign_edit' === $request->get('_route')) {
            return $selectedCampaign;
        }

        $clonedCampaign = clone $selectedCampaign;
        $clonedCampaign->setDealId(null);
        $clonedCampaign->setCreatedAt(new DateTime('now', new DateTimeZone('US/Eastern')));

        return $clonedCampaign;
    }

    /**
     * @return mixed
     */
    public function getCampaignEditValidationGroup(Request $request)
    {
        $validationGroup = self::VALIDATION_GROUP_EDIT;

        if (CostFieldType::TYPE_CPV == $request->request->get('campaign')['costType']) {
            $validationGroup = self::VALIDATION_GROUP_EDIT_CPV;
        }

        return $validationGroup;
    }

    public function getCampaignNewValidationGroup(Request $request, Campaign $campaign): string
    {
        $validationGroup = self::VALIDATION_GROUP_REGISTER;

        if ($campaign->getSsp() && 'lkqd' === strtolower($campaign->getSsp()->getName())) {
            $validationGroup = self::VALIDATION_GROUP_REGISTER_LKQD;

            if (CampaignFieldType::TYPE_DEAL === $campaign->getType()) {
                $validationGroup = self::VALIDATION_GROUP_REGISTER_DEAL;
            } elseif (CostFieldType::TYPE_CPV == $request->request->get('campaign')['costType']) {
                $validationGroup = self::VALIDATION_GROUP_REGISTER_CPV;
            }
        } else {
            $campaign->setType(CampaignFieldType::TYPE_DEAL);
        }

        return $validationGroup;
    }

    public function setCostType(Campaign $campaign, Request $request): void
    {
        if (CostFieldType::TYPE_CPV == $request->request->get('campaign')['costType']) {
            $campaign->setCpm(null);
        } else {
            $campaign->setCpv(null);
        }
    }
}
