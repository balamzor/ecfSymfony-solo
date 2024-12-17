<?php

// src/Controller/ReservationController.php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Workspace;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\WorkspaceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class ReservationController extends AbstractController
{
    private $entityManager;
    private $reservationRepository;

    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository)
    {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
    }

    #[Route('/reservation/{id}', name: 'app_reservation')]
    public function index(Request $request, $id, AuthorizationCheckerInterface $authChecker): Response
    {
        // if (!$authChecker->isGranted('ROLE_SUBSCRIBED')) {
        //     return $this->redirectToRoute('subscribe_user');
        // }

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $id = (int) $id;
        $workspace = $this->entityManager->getRepository(Workspace::class)->find($id);

        $reservation->setWorkspace($workspace);

        $user = $this->getUser();

        // Vérif si un utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour effectuer une réservation.');
            return $this->redirectToRoute('app_login');  // Rediriger vers la page de connexion
        }

        // recup utilisateur
        $reservation->setUser($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // disponibilité du créneau
            $startTime = $reservation->getRentalStart();
            $dayOfWeek = $startTime->format('N');
            $hour = $startTime->format('H');
            $endTime = $reservation->getRentalEnd();

            // Vérif que la réservation est entre 8h et 19h et du lundi au vendredi
            if ($hour < 8 || $hour > 19 || $dayOfWeek >= 6) {
                $this->addFlash('error', 'Les réservations sont autorisées uniquement du lundi au vendredi, entre 8h et 19h.');
                return $this->redirectToRoute('app_reservation', ['id' => $workspace->getId()]);
            }

            // Vérif que la réservation ne dépasse pas 19h
            $maxEndTime = new \DateTime($startTime->format('Y-m-d') . ' 19:00');

            // Comparer la fin de la réservation avec 19h
            if ($endTime > $maxEndTime) {
                $this->addFlash('error', 'Les réservations doivent être effectuées avant 19h00.');
                return $this->redirectToRoute('app_reservation', ['id' => $workspace->getId()]);
            }

            // Vérif de la durée de la réservation (entre 1 et 4 heures)
            $duration = $startTime->diff($endTime)->h;
            if ($duration < 1 || $duration > 4) {
                $this->addFlash('error', 'La durée de la réservation doit être entre 1 et 4 heures.');
                return $this->redirectToRoute('app_reservation', ['id' => $workspace->getId()]);
            }

            // disponibilité du créneau pour la salle
            $salle = $reservation->getWorkspace();
            $existingReservation = $this->reservationRepository->findOverlappingReservations($startTime, $endTime, $salle);

            if ($existingReservation) {
                $this->addFlash('error', 'Le créneau est déjà réservé.');
            } else {
                $this->entityManager->persist($reservation);
                $this->entityManager->flush();

                $this->addFlash('success', 'Réservation réussie!');
            }

            return $this->redirectToRoute('app_reservation', ['id' => $workspace->getId()]);
        }

        // Affichage des réservations existantes
        $reservations = $this->reservationRepository->findByWorkspaceId($id);

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'reservations' => $reservations,
            'workspace' => $workspace,
        ]);
    }

    #[Route('/reservation/events/{id}', name: 'reservation_events', methods: ['GET'])]
    public function getEvents($id): JsonResponse
    {
        $reservations = $this->reservationRepository->findByWorkspaceId($id);
        $user = $this->getUser();
        $events = [];

        foreach ($reservations as $reservation) {
            $eventColor = ($reservation->getUser() === $user) ? 'blue' : 'gray';
            $events[] = [
                'id' => $reservation->getId(),
                'title' => 'Réservé',
                'start' => $reservation->getRentalStart()->format('Y-m-d\TH:i:s'),
                'end' => $reservation->getRentalEnd()->format('Y-m-d\TH:i:s'),
                'color' => $eventColor,
                'user_id' => $reservation->getUser()->getId(),
            ];
        }

        return new JsonResponse($events);
    }

    #[Route('/reservation/create/{id}', name: 'app_reservation_create', methods: ['POST'])]
    public function createReservation(Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $start = new \DateTime($data['start']);
        $end = new \DateTime($data['end']);
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        // Restrictions horaires
        $startHour = (int) $start->format('H');
        $endHour = (int) $end->format('H');
        $startMinute = (int) $start->format('i');

        if ($start < $now) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas réserver une date passée.'], 400);
        }

        if ($startHour < 8 || $endHour > 19 || ($endHour === 19 && $startMinute > 0)) {
            return new JsonResponse(['error' => 'Les réservations sont autorisées uniquement entre 8h et 19h.'], 400);
        }

        $duration = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
        if ($duration < 1 || $duration > 4) {
            return new JsonResponse(['error' => 'La durée de la réservation doit être entre 1 et 4 heures.'], 400);
        }

        $workspace = $this->entityManager->getRepository(Workspace::class)->find($id);
        $existingReservation = $this->reservationRepository->findOverlappingReservations($start, $end, $workspace);

        if ($existingReservation) {
            return new JsonResponse(['error' => 'Le créneau est déjà réservé.'], 400);
        }

        // Création de la réservation
        $reservation = new Reservation();
        $reservation->setRentalStart($start);
        $reservation->setRentalEnd($end);
        $reservation->setWorkspace($workspace);
        $reservation->setUser($this->getUser());

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Réservation créée avec succès !']);
    }

    #[Route('/reservation/delete/{id}', name: 'app_reservation_delete', methods: ['DELETE'])]
    public function deleteReservation(Reservation $reservation): JsonResponse
    {
        // Vérifier que l'utilisateur connecté est celui qui a créé la réservation
        if ($reservation->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas supprimer une réservation qui ne vous appartient pas.'], 403);
        }

        // Supprimer la réservation
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        // Retourner une réponse JSON pour indiquer que la suppression a réussi
        return new JsonResponse(['success' => true]);
    }


    #[Route('/reservation/update/{id}', name: 'app_reservation_update', methods: ['POST'])]
    public function updateReservation(Request $request, Reservation $reservation): JsonResponse
    {
        // Vérifier que l'utilisateur connecté est celui qui a créé la réservation
        if ($reservation->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas modifier une réservation qui ne vous appartient pas.'], 403);
        }

        $data = json_decode($request->getContent(), true);

        // Récupérer les nouvelles dates de début et de fin
        $start = new \DateTime($data['start']);
        $end = new \DateTime($data['end']);

        // 1. Vérification de la durée de la réservation
        $duration = $start->diff($end)->h;
        if ($duration < 1 || $duration > 4) {
            return new JsonResponse(['error' => 'La durée de la réservation doit être entre 1 et 4 heures.'], 400);
        }

        // 2. Vérification que la réservation est entre 8h et 19h et du lundi au vendredi
        $startHour = (int) $start->format('H');
        $endHour = (int) $end->format('H');
        $dayOfWeek = $start->format('N');  // Jour de la semaine (1 = lundi, 7 = dimanche)

        if ($startHour < 8 || $endHour > 19 || ($endHour === 19 && (int) $end->format('i') > 0)) {
            return new JsonResponse(['error' => 'Les réservations sont autorisées uniquement entre 8h et 19h.'], 400);
        }

        if ($dayOfWeek >= 6) {  // 6 = samedi, 7 = dimanche
            return new JsonResponse(['error' => 'Les réservations sont autorisées uniquement du lundi au vendredi.'], 400);
        }

        // 3. Vérification de la disponibilité du créneau horaire (pas de réservation qui se chevauche)
        $workspace = $reservation->getWorkspace();
        $existingReservation = $this->reservationRepository->findOverlappingReservations($start, $end, $workspace);

        if ($existingReservation) {
            return new JsonResponse(['error' => 'Le créneau est déjà réservé.'], 400);
        }

        // Mise à jour de la réservation avec les nouvelles dates
        $reservation->setRentalStart($start);
        $reservation->setRentalEnd($end);

        // Enregistrer les modifications
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/reservation/{id}/manage', name: 'app_reservation_manage')]
    public function manageEvent($id, ReservationRepository $reservationRepository, WorkspaceRepository $workspaceRepository, Request $request, EntityManagerInterface $entityManager)
    {
        // Récupérer l'événement
        $reservation = $reservationRepository->find($id);

        // Si l'événement n'existe pas
        if (!$reservation) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        // Vérifier que l'utilisateur connecté est le créateur de l'événement
        if ($reservation->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_home'); // Redirige vers la page d'accueil si ce n'est pas l'utilisateur
        }

        // Récupérer le workspace associé à cet événement
        $workspace = $workspaceRepository->find($reservation->getWorkspace()->getId());

        // Créer un formulaire pour la modification de l'événement
        $form = $this->createFormBuilder($reservation)
            ->add('RentalStart', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'html5' => true,
                'data' => $reservation->getRentalStart()
            ])
            ->add('RentalEnd', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'html5' => true,
                'data' => $reservation->getRentalEnd()
            ])
            ->getForm();

        // Traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de la durée
            $startTime = $reservation->getRentalStart();
            $endTime = $reservation->getRentalEnd();
            $duration = $startTime->diff($endTime);
            $hours = $duration->h + ($duration->days * 24); // Calculer en heures

            // Vérification de la durée entre 1 et 4 heures
            if ($hours < 1 || $hours > 4) {
                $this->addFlash('error', 'La durée de la réservation doit être entre 1 et 4 heures.');
                return $this->redirectToRoute('app_reservation_manage', ['id' => $id]);
            }

            // Vérification des horaires
            $startHour = (int) $startTime->format('H');
            $endHour = (int) $endTime->format('H');
            $dayOfWeek = $startTime->format('N');

            if ($startHour < 8 || $endHour > 19 || ($endHour === 19 && (int) $endTime->format('i') > 0)) {
                $this->addFlash('error', 'Les réservations sont autorisées uniquement entre 8h et 19h.');
                return $this->redirectToRoute('app_reservation_manage', ['id' => $id]);
            }

            if ($dayOfWeek >= 6) {
                $this->addFlash('error', 'Les réservations sont autorisées uniquement du lundi au vendredi.');
                return $this->redirectToRoute('app_reservation_manage', ['id' => $id]);
            }

            // Vérification de la disponibilité du créneau horaire (pas de chevauchement)
            // Exclure l'événement actuel de la vérification des chevauchements
            $existingReservation = $this->reservationRepository->findOverlappingReservations($startTime, $endTime, $reservation->getWorkspace());
            foreach ($existingReservation as $existing) {
                // Si l'événement existant n'est pas celui qu'on est en train de modifier, on le bloque
                if ($existing->getId() !== $reservation->getId()) {
                    $this->addFlash('error', 'Le créneau est déjà réservé.');
                    return $this->redirectToRoute('app_reservation_manage', ['id' => $id]);
                }
            }

            // Enregistrer la modification
            $entityManager->flush();

            $this->addFlash('success', 'Réservation mise à jour avec succès!');
            return $this->redirectToRoute('app_reservation', ['id' => $workspace->getId()]);
        }

        return $this->render('reservation/manage.html.twig', [
            'reservation' => $reservation,
            'workspace' => $workspace,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/{id}', name: 'app_reservation_reservations', methods: ['GET'])]
    public function reservations(ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $reservation = $reservationRepository->findByWorkspaceId($id);
        $workspace = $entityManager->getRepository(Workspace::class)->find($id);

        return $this->render('admin_workplace/reservations.html.twig', [
            'reservation' => $reservation,
            'workspace' => $workspace,
        ]);
    }
}
