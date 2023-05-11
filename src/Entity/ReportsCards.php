<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportsCardsRepository")
 */
class ReportsCards
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $objective;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $revenue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $impressions;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $active_deals;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $viewability;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $active_direct_campaigns;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="reportsCards", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjective(): ?float
    {
        return $this->objective;
    }

    public function setObjective(?float $objective): self
    {
        $this->objective = $objective;

        return $this;
    }

    public function getRevenue(): ?float
    {
        return $this->revenue;
    }

    public function setRevenue(?float $revenue): self
    {
        $this->revenue = $revenue;

        return $this;
    }

    public function getImpressions(): ?float
    {
        return $this->impressions;
    }

    public function setImpressions(?float $impressions): self
    {
        $this->impressions = $impressions;

        return $this;
    }

    public function getActiveDeals(): ?float
    {
        return $this->active_deals;
    }

    public function setActiveDeals(?float $active_deals): self
    {
        $this->active_deals = $active_deals;

        return $this;
    }

    public function getViewability(): ?float
    {
        return $this->viewability;
    }

    public function setViewability(?float $viewability): self
    {
        $this->viewability = $viewability;

        return $this;
    }

    public function getActiveDirectCampaigns(): ?int
    {
        return $this->active_direct_campaigns;
    }

    public function setActiveDirectCampaigns(?int $active_direct_campaigns): self
    {
        $this->active_direct_campaigns = $active_direct_campaigns;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
