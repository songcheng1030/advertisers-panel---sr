<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $iso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $niceName;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $iso3;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $numCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phoneCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="country")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Agency", mappedBy="countries")
     */
    private $agencies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Campaign", mappedBy="countries")
     */
    private $campaigns;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->agencies = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIso(): ?string
    {
        return $this->iso;
    }

    public function setIso(?string $iso): self
    {
        $this->iso = $iso;

        return $this;
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

    public function getNiceName(): ?string
    {
        return $this->niceName;
    }

    public function setNiceName(?string $niceName): self
    {
        $this->niceName = $niceName;

        return $this;
    }

    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    public function setIso3(?string $iso3): self
    {
        $this->iso3 = $iso3;

        return $this;
    }

    public function getNumCode(): ?int
    {
        return $this->numCode;
    }

    public function setNumCode(?int $numCode): self
    {
        $this->numCode = $numCode;

        return $this;
    }

    public function getPhoneCode(): ?int
    {
        return $this->phoneCode;
    }

    public function setPhoneCode(?int $phoneCode): self
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCountry($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCountry() === $this) {
                $user->setCountry(null);
            }
        }

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
            $agency->addCountry($this);
        }

        return $this;
    }

    public function removeAgency(Agency $agency): self
    {
        if ($this->agencies->contains($agency)) {
            $this->agencies->removeElement($agency);
            $agency->removeCountry($this);
        }

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
            $campaign->addCountry($this);
        }

        return $this;
    }

    public function removeCampaign(Campaign $campaign): self
    {
        if ($this->campaigns->contains($campaign)) {
            $this->campaigns->removeElement($campaign);
            $campaign->removeCountry($this);
        }

        return $this;
    }
}
