<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TargetRepository")
 */
class Target
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="targets")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $goal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reached = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGoal(): ?int
    {
        return $this->goal;
    }

    public function setGoal(?int $goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    public function getReached(): ?int
    {
        return $this->reached;
    }

    public function setReached(?int $reached): self
    {
        $this->reached = $reached;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): self
    {
        $this->month = $month;

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

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function fromArray(array $values): self
    {
        foreach ($values as $field => $value) {
            if (!is_array($value) && property_exists($this, $field)) {
                $this->$field = $value;
            }
        }

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata
            ->addPropertyConstraint('goal', new Assert\NotBlank([
                'message' => 'common.field_must_be_completed',
            ]));
    }
}
