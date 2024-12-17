<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $RentalStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $RentalEnd = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Workspace $workspace = null;

    public function __construct()
    {
        $this->RentalStart = new \DateTime();
        $this->RentalEnd = new \DateTime();
    }

    public function prePersist()
    {
        $this->RentalStart = new \DateTime();

        $start = $this->RentalStart;
        $this->RentalEnd = date_add($start, date_interval_create_from_date_string("6 days"));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRentalStart(): ?\DateTimeInterface
    {
        return $this->RentalStart;
    }

    public function setRentalStart(\DateTimeInterface $RentalStart): static
    {
        $this->RentalStart = $RentalStart;

        return $this;
    }

    public function getRentalEnd(): ?\DateTimeInterface
    {
        return $this->RentalEnd;
    }

    public function setRentalEnd(\DateTimeInterface $RentalEnd): static
    {
        $this->RentalEnd = $RentalEnd;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): static
    {
        $this->workspace = $workspace;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->getUser()->getId();
    }
}
