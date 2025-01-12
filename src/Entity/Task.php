<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\TaskRepository;
use App\State\SetOwnerProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource(
    operations: [new Get(), new GetCollection(), new Post(), new Patch(), new Delete()],
    processor: SetOwnerProcessor::class
)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameEnglish = null;

    #[ORM\Column(length: 255)]
    private ?string $nameGeorgian = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEnglish = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionGeorgian = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readable: false, writable: false)]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    public function setNameEnglish(string $nameEnglish): static
    {
        $this->nameEnglish = $nameEnglish;

        return $this;
    }

    public function getNameGeorgian(): ?string
    {
        return $this->nameGeorgian;
    }

    public function setNameGeorgian(string $nameGeorgian): static
    {
        $this->nameGeorgian = $nameGeorgian;

        return $this;
    }

    public function getDescriptionEnglish(): ?string
    {
        return $this->descriptionEnglish;
    }

    public function setDescriptionEnglish(?string $descriptionEnglish): static
    {
        $this->descriptionEnglish = $descriptionEnglish;

        return $this;
    }

    public function getDescriptionGeorgian(): ?string
    {
        return $this->descriptionGeorgian;
    }

    public function setDescriptionGeorgian(?string $descriptionGeorgian): static
    {
        $this->descriptionGeorgian = $descriptionGeorgian;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOnwer(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
