<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Index(columns: ['created'], name: 'created_idx')]
#[ORM\Index(columns: ['creator_id'], name: 'creator_idx')]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Client $creator;

    #[ORM\ManyToOne(targetEntity: Vico::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Vico $vico;

    #[ORM\OneToOne(targetEntity: Rate::class, mappedBy: 'project')]
    private ?Rate $rate = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setCreator(Client $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function setVico(Vico $vico): static
    {
        $this->vico = $vico;

        return $this;
    }

    public function isRated(): bool
    {
        return null !== $this->rate;
    }

    public function getRate(): ?Rate
    {
        return $this->rate;
    }

    public function setRate(Rate $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
