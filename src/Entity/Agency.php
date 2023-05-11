<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AgencyRepository")
 */
class Agency
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="agencies")
     */
    private $salesManager;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rebate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $billingFiscalState;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingNifCif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingCompany;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     */
    private $billingCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingProvince;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingCp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Campaign", mappedBy="agency")
     */
    private $campaigns;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $accountManager = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Country", inversedBy="agencies", fetch="EAGER")
     */
    private $countries;

    public function __construct()
    {
        $this->accountManager = [];
        $this->campaigns = new ArrayCollection();
        $this->countries = new ArrayCollection();
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSalesManager(): ?User
    {
        return $this->salesManager;
    }

    public function setSalesManager(?User $salesManager): self
    {
        $this->salesManager = $salesManager;

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

    public function getBillingFiscalState(): ?int
    {
        return $this->billingFiscalState;
    }

    public function setBillingFiscalState(?int $billingFiscalState): self
    {
        $this->billingFiscalState = $billingFiscalState;

        return $this;
    }

    public function getBillingNifCif(): ?string
    {
        return $this->billingNifCif;
    }

    public function setBillingNifCif(?string $billingNifCif): self
    {
        $this->billingNifCif = $billingNifCif;

        return $this;
    }

    public function getBillingCompany(): ?string
    {
        return $this->billingCompany;
    }

    public function setBillingCompany(?string $billingCompany): self
    {
        $this->billingCompany = $billingCompany;

        return $this;
    }

    public function getBillingCountry(): ?Country
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(?Country $billingCountry): self
    {
        $this->billingCountry = $billingCountry;

        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(?string $billingCity): self
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    public function getBillingProvince(): ?string
    {
        return $this->billingProvince;
    }

    public function setBillingProvince(?string $billingProvince): self
    {
        $this->billingProvince = $billingProvince;

        return $this;
    }

    public function getBillingCp(): ?string
    {
        return $this->billingCp;
    }

    public function setBillingCp(?string $billingCp): self
    {
        $this->billingCp = $billingCp;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

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

    /**
     * @return Collection|Campaign[]
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    public function addCampaign(Campaign $campaign): self
    {
        if (!$this->campaigns->contains($campaign)) {
            $this->campaigns[] = $campaign;
            $campaign->setAgency($this);
        }

        return $this;
    }

    public function removeCampaign(Campaign $campaign): self
    {
        if ($this->campaigns->contains($campaign)) {
            $this->campaigns->removeElement($campaign);
            // set the owning side to null (unless already changed)
            if ($campaign->getAgency() === $this) {
                $campaign->setAgency(null);
            }
        }

        return $this;
    }

    public function getAccountManager(): ?array
    {
        return $this->accountManager;
    }

    public function setAccountManager(?array $accountManager): self
    {
        $this->accountManager = $accountManager;

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
            ]))
            ->addPropertyConstraint('salesManager', new Assert\NotBlank([
                'message' => 'common.field_must_be_selected',
            ]))
            ->addPropertyConstraint('rebate', new Assert\NotBlank([
                'message' => 'common.field_must_be_completed',
            ]))
            ->addPropertyConstraint('rebate', new Assert\Range([
                'min' => 0,
                'max' => 100,
                'minMessage' => 'common.field_min_message',
                'maxMessage' => 'common.field_max_message',
            ]));
    }
}
