<?php

namespace App\Entity;

use App\Validator\FixedRangeValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CampaignRepository")
 */
class Campaign
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agency", inversedBy="campaigns")
     */
    private $agency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Advertiser", inversedBy="campaigns")
     */
    private $advertiser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ssp", inversedBy="campaigns")
     */
    private $ssp;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dealId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Dsp", inversedBy="campaigns")
     */
    private $dsp;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vtr;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $viewability;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ctr;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $volume;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $listType;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $list = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cpm;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rebate;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Country", inversedBy="campaigns")
     */
    private $countries;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cpv;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vtrFrom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vtrTo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $viewabilityFrom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $viewabilityTo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ctrFrom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ctrTo;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="campaigns")
     * @Serializer\Exclude()
     */
    private $users;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self
    {
        $this->agency = $agency;

        return $this;
    }

    public function getAdvertiser(): ?Advertiser
    {
        return $this->advertiser;
    }

    public function setAdvertiser(?Advertiser $advertiser): self
    {
        $this->advertiser = $advertiser;

        return $this;
    }

    public function getSsp(): ?Ssp
    {
        return $this->ssp;
    }

    public function setSsp(?Ssp $ssp): self
    {
        $this->ssp = $ssp;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDealId(): ?string
    {
        return $this->dealId;
    }

    public function setDealId(?string $dealId): self
    {
        $this->dealId = $dealId;

        return $this;
    }

    public function getDsp(): ?Dsp
    {
        return $this->dsp;
    }

    public function setDsp(?Dsp $dsp): self
    {
        $this->dsp = $dsp;

        return $this;
    }

    public function getVtr(): ?int
    {
        return $this->vtr;
    }

    public function setVtr(?int $vtr): self
    {
        $this->vtr = $vtr;

        return $this;
    }

    public function getViewability(): ?int
    {
        return $this->viewability;
    }

    public function setViewability(?int $viewability): self
    {
        $this->viewability = $viewability;

        return $this;
    }

    public function getCtr(): ?int
    {
        return $this->ctr;
    }

    public function setCtr(?int $ctr): self
    {
        $this->ctr = $ctr;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(?int $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getListType(): ?int
    {
        return $this->listType;
    }

    public function setListType(?int $listType): self
    {
        $this->listType = $listType;

        return $this;
    }

    public function getList(): ?array
    {
        return $this->list;
    }

    public function setList(?array $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getCpm(): ?float
    {
        return $this->cpm;
    }

    public function setCpm(?float $cpm): self
    {
        $this->cpm = $cpm;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getRebate(): ?int
    {
        return $this->rebate;
    }

    public function setRebate(?int $rebate): self
    {
        $this->rebate = $rebate;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return Collection|Country[]
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->countries->contains($country)) {
            $this->countries->removeElement($country);
        }

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata
            ->addPropertyConstraint('name', new Assert\NotBlank([
                'message' => 'common.field_must_be_completed',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('name', new Assert\Length([
                'min' => 6,
                'max' => 256,
                'minMessage' => 'common.field_min_message',
                'maxMessage' => 'common.field_max_message',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('agency', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('advertiser', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('ssp', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('type', new Assert\NotBlank([
                'message' => 'common.field_must_be_completed',
                'groups' => ['register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('dealId', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('dsp', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
                'groups' => ['register', 'register-deal'],
            ]))
            ->addPropertyConstraint('vtr', new Assert\Range([
                'min' => 0,
                'max' => 100,
                'minMessage' => 'common.field_min_message',
                'maxMessage' => 'common.field_max_message',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('viewability', new Assert\Range([
                'min' => 0,
                'max' => 100,
                'minMessage' => 'common.field_min_message',
                'maxMessage' => 'common.field_max_message',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('ctr', new Assert\Range([
                'min' => 0,
                'max' => 100,
                'minMessage' => 'common.field_min_message',
                'maxMessage' => 'common.field_max_message',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('cpm', new Assert\NotBlank([
                'message' => 'common.field_must_be_completed',
                'groups' => ['register', 'register-lkqd', 'edit', 'register-deal'],
            ]))
            ->addPropertyConstraint('cpv', new Assert\NotBlank([
                'message' => 'common.field_must_be_completed',
                'groups' => ['register-cpv', 'edit-cpv'],
            ]))
            ->addPropertyConstraint('startAt', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('rebate', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addPropertyConstraint('rebate', new Assert\Range([
                'min' => 0,
                'max' => 100,
                'minMessage' => 'common.field_min_message',
                'maxMessage' => 'common.field_max_message',
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]))
            ->addConstraint(new Assert\Callback([
                'callback' => [
                    FixedRangeValidator::class,
                    'validate',
                ],
                'groups' => ['register', 'register-lkqd', 'register-cpv', 'edit', 'edit-cpv', 'register-deal'],
            ]));
    }

    public function getCpv(): ?float
    {
        return $this->cpv;
    }

    public function setCpv(?float $cpv): self
    {
        $this->cpv = $cpv;

        return $this;
    }

    public function getVtrFrom(): ?int
    {
        return $this->vtrFrom;
    }

    public function setVtrFrom(?int $vtrFrom): self
    {
        $this->vtrFrom = $vtrFrom;

        return $this;
    }

    public function getVtrTo(): ?int
    {
        return $this->vtrTo;
    }

    public function setVtrTo(?int $vtrTo): self
    {
        $this->vtrTo = $vtrTo;

        return $this;
    }

    public function getViewabilityFrom(): ?int
    {
        return $this->viewabilityFrom;
    }

    public function setViewabilityFrom(?int $viewabilityFrom): self
    {
        $this->viewabilityFrom = $viewabilityFrom;

        return $this;
    }

    public function getViewabilityTo(): ?int
    {
        return $this->viewabilityTo;
    }

    public function setViewabilityTo(?int $viewabilityTo): self
    {
        $this->viewabilityTo = $viewabilityTo;

        return $this;
    }

    public function getCtrFrom(): ?int
    {
        return $this->ctrFrom;
    }

    public function setCtrFrom(?int $ctrFrom): self
    {
        $this->ctrFrom = $ctrFrom;

        return $this;
    }

    public function getCtrTo(): ?int
    {
        return $this->ctrTo;
    }

    public function setCtrTo(?int $ctrTo): self
    {
        $this->ctrTo = $ctrTo;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $u): self
    {
        if (!$this->users->contains($u)) {
            $this->users[] = $u;
        }

        return $this;
    }

    public function removeUser(User $u): self
    {
        if ($this->users->contains($u)) {
            $this->users->removeElement($u);
        }

        return $this;
    }
}
