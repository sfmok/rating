<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RateRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: RateRepository::class)]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('rate:read')]
    private int $id;

    #[ORM\Column]
    #[Groups('rate:read')]
    private int $satisfaction;

    #[ORM\Column]
    #[Groups('rate:read')]
    private int $communication;

    #[ORM\Column]
    #[Groups('rate:read')]
    #[SerializedName('quality_of_work')]
    private int $qualityOfWork;

    #[ORM\Column]
    #[Groups('rate:read')]
    #[SerializedName('value_for_money')]
    private int $valueForMoney;

    #[ORM\Column(type: 'text')]
    #[Groups('rate:read')]
    private string $feedback;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    #[Groups('rate:read')]
    #[SerializedName('created_at')]
    private \DateTimeInterface $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups('rate:read')]
    #[SerializedName('updated_at')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToOne(targetEntity: Project::class, inversedBy: 'rate')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSatisfaction(): int
    {
        return $this->satisfaction;
    }

    public function setSatisfaction(int $satisfaction): static
    {
        $this->satisfaction = $satisfaction;

        return $this;
    }

    public function getCommunication(): int
    {
        return $this->communication;
    }

    public function setCommunication(int $communication): static
    {
        $this->communication = $communication;

        return $this;
    }

    public function getQualityOfWork(): int
    {
        return $this->qualityOfWork;
    }

    public function setQualityOfWork(int $qualityOfWork): static
    {
        $this->qualityOfWork = $qualityOfWork;

        return $this;
    }

    public function getValueForMoney(): int
    {
        return $this->valueForMoney;
    }

    public function setValueForMoney(int $valueForMoney): static
    {
        $this->valueForMoney = $valueForMoney;

        return $this;
    }

    public function getFeedback(): string
    {
        return $this->feedback;
    }

    public function setFeedback(string $feedback): static
    {
        $this->feedback = $feedback;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
