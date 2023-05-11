<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertiserRepository")
 */
class Advertiser
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Campaign", mappedBy="advertiser")
     */
    private $campaigns;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->campaigns = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
            $campaign->setAdvertiser($this);
        }

        return $this;
    }

    public function removeCampaign(Campaign $campaign): self
    {
        if ($this->campaigns->contains($campaign)) {
            $this->campaigns->removeElement($campaign);
            // set the owning side to null (unless already changed)
            if ($campaign->getAdvertiser() === $this) {
                $campaign->setAdvertiser(null);
            }
        }

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank([
            'message' => 'common.field_must_be_completed',
            'groups' => ['advertiser', 'advertiser-user'],
        ]));
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
