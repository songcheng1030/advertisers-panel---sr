<?php

namespace App\Entity;

use App\Repository\DemoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DemoRepository::class)
 */
class Demo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $url_format;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $format;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $supply_source_desktop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $supply_source_mobile;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $template;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $width_mobile;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $height_mobile;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $desktop;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $video;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $click_url;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $orientation;

    public function __construct($url_format = 'demo', $url = null, $format = null, $supply_source_desktop = null, $supply_source_mobile = null, $template = 'default', $width = null, $height = null, $width_mobile = null, $height_mobile = null, $desktop = false, $mobile = false, $status = 0, $video = null, $click_url = null)
    {
        $this->url_format = $url_format;
        $this->url = $url;
        $this->format = $format;
        $this->supply_source_desktop = $supply_source_desktop;
        $this->supply_source_mobile = $supply_source_mobile;
        $this->template = $template;
        $this->width = $width;
        $this->height = $height;
        $this->width_mobile = $width_mobile;
        $this->height_mobile = $height_mobile;
        $this->desktop = $desktop;
        $this->mobile = $mobile;
        $this->status = $status;
        $this->video = $video;
        $this->click_url = $click_url;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlFormat(): ?string
    {
        return $this->url_format;
    }

    public function setUrlFormat(string $url_format): self
    {
        $this->url_format = $url_format;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getSupplySourceDesktop(): ?string
    {
        return $this->supply_source_desktop;
    }

    public function setSupplySourceDesktop(?string $supply_source_desktop): self
    {
        $this->supply_source_desktop = $supply_source_desktop;

        return $this;
    }

    public function getSupplySourceMobile(): ?string
    {
        return $this->supply_source_mobile;
    }

    public function setSupplySourceMobile(?string $supply_source_mobile): self
    {
        $this->supply_source_mobile = $supply_source_mobile;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(?string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidthMobile(): ?string
    {
        return $this->width_mobile;
    }

    public function setWidthMobile(?string $width_mobile): self
    {
        $this->width_mobile = $width_mobile;

        return $this;
    }

    public function getHeightMobile(): ?string
    {
        return $this->height_mobile;
    }

    public function setHeightMobile(?string $height_mobile): self
    {
        $this->height_mobile = $height_mobile;

        return $this;
    }

    public function getDesktop(): ?bool
    {
        return $this->desktop;
    }

    public function setDesktop(?bool $desktop): self
    {
        $this->desktop = $desktop;

        return $this;
    }

    public function getMobile(): ?bool
    {
        return $this->mobile;
    }

    public function setMobile(?bool $mobile): self
    {
        $this->mobile = $mobile;

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

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getClickUrl(): ?string
    {
        return $this->click_url;
    }

    public function setClickUrl(?string $click_url): self
    {
        $this->click_url = $click_url;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(?string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }
}
