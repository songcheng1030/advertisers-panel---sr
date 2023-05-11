<?php

namespace App\Entity;

use App\Form\RoleType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /**
     * @Serializer\Exclude()
     */
    public static $adminGroupUsers = [
        RoleType::ROLE_ADMIN,
        RoleType::ROLE_SALES_MANAGER,
        RoleType::ROLE_SALES_MANAGER_HEAD,
        RoleType::ROLE_CAMPAIGN_VIEWER,
    ];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Serializer\Exclude()
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="users")
     * @Serializer\Exclude()
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Serializer\Exclude()
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Serializer\Exclude()
     */
    private $ipAddress;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Serializer\Exclude()
     */
    private $salesManagerHead;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nick;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="salesManagers")
     * @Serializer\Exclude()
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $salesManagers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="users")
     * @Serializer\Exclude()
     */
    private $country;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $monthlyTarget;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showGlobalStats;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agency", mappedBy="salesManager")
     * @Serializer\Exclude()
     */
    private $agencies;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Target", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $targets;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ReportsCards", mappedBy="user", cascade={"persist", "remove"})
     */
    private $reportsCards;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEmailNotificationEnabled;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Campaign", inversedBy="users")
     * @Serializer\Exclude()
     */
    private $campaigns;

    public function __construct()
    {
        $this->salesManagers = new ArrayCollection();
        $this->agencies = new ArrayCollection();
        $this->targets = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getName() . ' ' . $this->getLastName();
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?self
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?self $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getSalesManagerHead(): ?self
    {
        return $this->salesManagerHead;
    }

    public function setSalesManagerHead(?self $salesManagerHead): self
    {
        $this->salesManagerHead = $salesManagerHead;

        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function getUser(): ?self
    {
        return $this->user;
    }

    public function setUser(?self $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSalesManagers(): Collection
    {
        return $this->salesManagers;
    }

    public function addSalesManager(self $salesManager): self
    {
        if (!$this->salesManagers->contains($salesManager)) {
            $this->salesManagers[] = $salesManager;
            $salesManager->setUser($this);
        }

        return $this;
    }

    public function removeSalesManager(self $salesManager): self
    {
        if ($this->salesManagers->contains($salesManager)) {
            $this->salesManagers->removeElement($salesManager);
            // set the owning side to null (unless already changed)
            if ($salesManager->getUser() === $this) {
                $salesManager->setUser(null);
            }
        }

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getMonthlyTarget(): ?float
    {
        return $this->monthlyTarget;
    }

    public function setMonthlyTarget(?float $monthlyTarget): self
    {
        $this->monthlyTarget = $monthlyTarget;

        return $this;
    }

    public function getShowGlobalStats(): ?bool
    {
        return $this->showGlobalStats;
    }

    public function setShowGlobalStats(?bool $showGlobalStats): self
    {
        $this->showGlobalStats = $showGlobalStats;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return Collection|Agency[]
     */
    public function getAgencies(): Collection
    {
        return $this->agencies;
    }

    public function addAgency(Agency $agency): self
    {
        if (!$this->agencies->contains($agency)) {
            $this->agencies[] = $agency;
            $agency->setSalesManager($this);
        }

        return $this;
    }

    public function removeAgency(Agency $agency): self
    {
        if ($this->agencies->contains($agency)) {
            $this->agencies->removeElement($agency);
            // set the owning side to null (unless already changed)
            if ($agency->getSalesManager() === $this) {
                $agency->setSalesManager(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isAdmin()
    {
        return in_array(RoleType::ROLE_ADMIN, $this->roles);
    }

    public function isAdvertiser()
    {
        return in_array('ROLE_ADVERTISER', $this->roles);
    }

    public function isSalesManagerHead()
    {
        return in_array(RoleType::ROLE_SALES_MANAGER_HEAD, $this->roles);
    }

    public function isSalesManager()
    {
        return in_array(RoleType::ROLE_SALES_MANAGER, $this->roles);
    }

    public function isCampaignViewer()
    {
        return in_array(RoleType::ROLE_CAMPAIGN_VIEWER, $this->roles);
    }

    public function canCreateAccounts(): bool
    {
        return $this->isAdmin() || $this->isSalesManagerHead();
    }

    public function isAllowedToImpersonate(): bool
    {
        return $this->isAdmin() || $this->isSalesManagerHead();
    }

    public function canImpersonateUser(User $user): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isSalesManagerHead()) {
            if ($user->isSalesManager()) {
                return $user->getSalesManagerHead() === $this;
            }
        }

        return false;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('username', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'advertiser-user',
                'advertiser-user-edit',
                'advertiser-user-edit-password',
            ],
        ]))->addConstraint(new UniqueEntity([
            'fields' => 'username',
            'message' => 'common.field_already_exist',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'advertiser-user',
                'advertiser-user-edit',
                'advertiser-user-edit-password',
            ],
        ]))->addPropertyConstraint('plainPassword', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'advertiser-user',
                'advertiser-user-edit-password',
            ],
        ]))->addPropertyConstraint('plainPassword', new Assert\Length([
            'min' => 8,
            'max' => 20,
            'minMessage' => 'common.field_min_message',
            'maxMessage' => 'common.field_max_message',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'account-edition',
                'account-edition-admin',
                'account-edition-managers',
                'advertiser-user',
                'advertiser-user-edit',
                'advertiser-user-edit-password',
            ],
        ]))->addPropertyConstraint('name', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'account-edition',
                'account-edition-admin',
                'account-edition-managers',
            ],
        ]))->addPropertyConstraint('name', new Assert\Length([
            'min' => 3,
            'max' => 20,
            'minMessage' => 'El Nombre debe tener al menos {{ limit }} caracteres',
            'maxMessage' => 'El Nombre no puede tener mas de {{ limit }} caracteres',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'account-edition',
                'account-edition-admin',
                'account-edition-managers',
            ],
        ]))->addPropertyConstraint('lastName', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'account-edition',
                'account-edition-admin',
                'account-edition-managers',
            ],
        ]))->addPropertyConstraint('lastName', new Assert\Length([
            'min' => 3,
            'max' => 50,
            'minMessage' => 'El Apellido debe tener al menos {{ limit }} caracteres',
            'maxMessage' => 'El Apellido no puede tener mas de {{ limit }} caracteres',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'account-edition',
                'account-edition-admin',
                'account-edition-managers',
            ],
        ]))->addPropertyConstraint('email', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'advertiser-user',
                'advertiser-user-edit',
                'advertiser-user-edit-password',
            ],
        ]))->addPropertyConstraint('locale', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'advertiser-user',
                'advertiser-user-edit',
                'advertiser-user-edit-password',
            ],
        ]))->addPropertyConstraint('roles', new Assert\NotBlank([
            'message' => 'common.field_must_be_selected',
            'groups' => ['account-creation', 'account-creation-managers'],
        ]))->addPropertyConstraint('locale', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => ['account-creation', 'account-creation-manager-head'],
        ]))->addPropertyConstraint('nick', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'account-edition',
                'account-edition-admin',
                'account-edition-managers',
            ],
        ]))->addPropertyConstraint('nick', new Assert\Length([
            'min' => 2,
            'max' => 20,
            'minMessage' => 'common.field_min_message',
            'maxMessage' => 'common.field_max_message',
            'exactMessage' => 'common.field_exact_message',
            'groups' => [
                'account-creation',
                'account-creation-manager-head',
                'account-edition',
                'account-edition-admin',
                'account-edition-managers',
            ],
        ]));
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return Collection|Target[]
     */
    public function getTargets(): Collection
    {
        return $this->targets;
    }

    public function addTarget(Target $target): self
    {
        if (!$this->targets->contains($target)) {
            $this->targets[] = $target;
            $target->setUser($this);
        }

        return $this;
    }

    public function removeTarget(Target $target): self
    {
        if ($this->targets->contains($target)) {
            $this->targets->removeElement($target);
            // set the owning side to null (unless already changed)
            if ($target->getUser() === $this) {
                $target->setUser(null);
            }
        }

        return $this;
    }

    public function isEnabled()
    {
        return self::STATUS_ACTIVE === $this->status;
    }

    public function getReportsCards(): ?ReportsCards
    {
        return $this->reportsCards;
    }

    public function setReportsCards(ReportsCards $reportsCards): self
    {
        $this->reportsCards = $reportsCards;

        // set the owning side of the relation if necessary
        if ($reportsCards->getUser() !== $this) {
            $reportsCards->setUser($this);
        }

        return $this;
    }

    public function getIsEmailNotificationEnabled(): ?bool
    {
        return $this->isEmailNotificationEnabled;
    }

    public function setIsEmailNotificationEnabled(?bool $isEmailNotificationEnabled): self
    {
        $this->isEmailNotificationEnabled = $isEmailNotificationEnabled;

        return $this;
    }

    /**
     * @return Collection|Campaign[]
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    public function addCampaign(Campaign $c): self
    {
        if (!$this->campaigns->contains($c)) {
            $this->campaigns[] = $c;
            $c->addUser($this);
        }

        return $this;
    }

    public function removeCampaign(Campaign $c): self
    {
        if ($this->campaigns->contains($c)) {
            $this->campaigns->removeElement($c);
            $c->removeUser($this);
        }

        return $this;
    }
}
