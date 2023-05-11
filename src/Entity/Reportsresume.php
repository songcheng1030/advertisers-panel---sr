<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportsresumeRepository")
 */
class Reportsresume
{
    const TABLE_PREFIX = 'reportsresume';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $iduser;

    /**
     * @ORM\Column(type="integer")
     */
    private $idTag;

    /**
     * @ORM\Column(type="integer")
     */
    private $Domain;

    /**
     * @ORM\Column(type="integer")
     */
    private $Country;

    /**
     * @ORM\Column(type="integer")
     */
    private $Impressions;

    /**
     * @ORM\Column(type="integer")
     */
    private $Opportunities;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=16)
     */
    private $Revenue;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=16)
     */
    private $Coste;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    private $ExtraPrimaP;

    /**
     * @ORM\Column(type="integer")
     */
    private $Clicks;

    /**
     * @ORM\Column(type="integer")
     */
    private $Wins;

    /**
     * @ORM\Column(type="integer")
     */
    private $adStarts;

    /**
     * @ORM\Column(type="integer")
     */
    private $FirstQuartiles;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=16)
     */
    private $Extraprima;

    /**
     * @ORM\Column(type="integer")
     */
    private $MidViews;

    /**
     * @ORM\Column(type="integer")
     */
    private $ThirdQuartiles;

    /**
     * @ORM\Column(type="integer")
     */
    private $CompletedViews;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeAdded;

    /**
     * @ORM\Column(type="integer")
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="date")
     */
    private $Date;

    /**
     * @ORM\Column(type="integer")
     */
    private $idsite;

    /**
     * @ORM\Column(type="integer")
     */
    private $formatloads;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(int $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getIdTag(): ?int
    {
        return $this->idTag;
    }

    public function setIdTag(int $idTag): self
    {
        $this->idTag = $idTag;

        return $this;
    }

    public function getDomain(): ?int
    {
        return $this->Domain;
    }

    public function setDomain(int $Domain): self
    {
        $this->Domain = $Domain;

        return $this;
    }

    public function getCountry(): ?int
    {
        return $this->Country;
    }

    public function setCountry(int $Country): self
    {
        $this->Country = $Country;

        return $this;
    }

    public function getImpressions(): ?int
    {
        return $this->Impressions;
    }

    public function setImpressions(int $Impressions): self
    {
        $this->Impressions = $Impressions;

        return $this;
    }

    public function getOpportunities(): ?int
    {
        return $this->Opportunities;
    }

    public function setOpportunities(int $Opportunities): self
    {
        $this->Opportunities = $Opportunities;

        return $this;
    }

    public function getRevenue()
    {
        return $this->Revenue;
    }

    public function setRevenue($Revenue): self
    {
        $this->Revenue = $Revenue;

        return $this;
    }

    public function getCoste()
    {
        return $this->Coste;
    }

    public function setCoste($Coste): self
    {
        $this->Coste = $Coste;

        return $this;
    }

    public function getExtraPrimaP()
    {
        return $this->ExtraPrimaP;
    }

    public function setExtraPrimaP($ExtraPrimaP): self
    {
        $this->ExtraPrimaP = $ExtraPrimaP;

        return $this;
    }

    public function getClicks(): ?int
    {
        return $this->Clicks;
    }

    public function setClicks(int $Clicks): self
    {
        $this->Clicks = $Clicks;

        return $this;
    }

    public function getWins(): ?int
    {
        return $this->Wins;
    }

    public function setWins(int $Wins): self
    {
        $this->Wins = $Wins;

        return $this;
    }

    public function getAdStarts(): ?int
    {
        return $this->adStarts;
    }

    public function setAdStarts(int $adStarts): self
    {
        $this->adStarts = $adStarts;

        return $this;
    }

    public function getFirstQuartiles(): ?int
    {
        return $this->FirstQuartiles;
    }

    public function setFirstQuartiles(int $FirstQuartiles): self
    {
        $this->FirstQuartiles = $FirstQuartiles;

        return $this;
    }

    public function getExtraprima()
    {
        return $this->Extraprima;
    }

    public function setExtraprima($Extraprima): self
    {
        $this->Extraprima = $Extraprima;

        return $this;
    }

    public function getMidViews(): ?int
    {
        return $this->MidViews;
    }

    public function setMidViews(int $MidViews): self
    {
        $this->MidViews = $MidViews;

        return $this;
    }

    public function getThirdQuartiles(): ?int
    {
        return $this->ThirdQuartiles;
    }

    public function setThirdQuartiles(int $ThirdQuartiles): self
    {
        $this->ThirdQuartiles = $ThirdQuartiles;

        return $this;
    }

    public function getCompletedViews(): ?int
    {
        return $this->CompletedViews;
    }

    public function setCompletedViews(int $CompletedViews): self
    {
        $this->CompletedViews = $CompletedViews;

        return $this;
    }

    public function getTimeAdded(): ?int
    {
        return $this->timeAdded;
    }

    public function setTimeAdded(int $timeAdded): self
    {
        $this->timeAdded = $timeAdded;

        return $this;
    }

    public function getLastUpdate(): ?int
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(int $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getIdsite(): ?int
    {
        return $this->idsite;
    }

    public function setIdsite(int $idsite): self
    {
        $this->idsite = $idsite;

        return $this;
    }

    public function getFormatloads(): ?int
    {
        return $this->formatloads;
    }

    public function setFormatloads(int $formatloads): self
    {
        $this->formatloads = $formatloads;

        return $this;
    }
}
