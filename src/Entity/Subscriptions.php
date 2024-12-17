<?php

namespace App\Entity;

use App\Repository\SubscriptionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionsRepository::class)]
class Subscriptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
}
// class Subscriptions
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     private ?int $id = null;

//     #[ORM\Column(type: Types::DATETIME_MUTABLE)]
//     private ?\DateTimeInterface $dateDebut = null;

//     #[ORM\Column(type: Types::DATETIME_MUTABLE)]
//     private ?\DateTimeInterface $dateFin = null;

//     #[ORM\Column(type: 'integer', nullable: false)]
//     private ?int $idUser = null;

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getDateDebut(): ?\DateTimeInterface
//     {
//         return $this->dateDebut;
//     }

//     public function setDateDebut(\DateTimeInterface $dateDebut): static
//     {
//         $this->dateDebut = $dateDebut;

//         return $this;
//     }

//     public function getDateFin(): ?\DateTimeInterface
//     {
//         return $this->dateFin;
//     }

//     public function setDateFin(\DateTimeInterface $dateFin): static
//     {
//         $this->dateFin = $dateFin;

//         return $this;
//     }

//     public function getIdUser(): ?int
//     {
//         return $this->idUser;
//     }

//     public function setIdUser(int $idUser): static
//     {
//         $this->idUser = $idUser;

//         return $this;
//     }
// }