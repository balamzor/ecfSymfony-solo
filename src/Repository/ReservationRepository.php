<?php

// src/Repository/ReservationRepository.php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    // Cette méthode vérifie si une réservation chevauche une autre.
    public function findOverlappingReservations($startTime, $endTime, $salle)
    {
        return $this->createQueryBuilder('r')
            ->where('r.RentalStart < :RentalEnd')
            ->andWhere('r.RentalEnd > :RentalStart')
            ->andWhere('r.workspace = :salle')
            ->setParameter('RentalStart', $startTime)
            ->setParameter('RentalEnd', $endTime)
            ->setParameter('salle', $salle)
            ->getQuery()
            ->getResult();
    }

    public function findByWorkspaceId($workspaceId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.workspace = :workspaceId')
            ->setParameter('workspaceId', $workspaceId)
            ->orderBy('r.RentalStart', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
