<?php

namespace App\Controller;


use App\Form\PaymentType;
use App\Form\EditUserType;
use App\Entity\Reservation;
use App\Form\SubscribeType;
use App\Entity\Subscriptions;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ReservationController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class UserController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, ReservationController $reservations)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->reservations = $reservations;

    }
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/subscribe', name: 'subscribe_user')]
    public function subscribe(Request $request): Response
    {
        return $this->render('user/subscribe.html.twig', [
            // 'formMensuel' => $formMensuel->createView(),
            // 'formAnnuel' => $formAnnuel->createView(),
        ]);
    }
    #[Route('/user/payment/{id}', name: 'payment_user', methods: ['GET', 'POST'])]
    public function payment(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $user = $this->getUser();

        $prix = 0;
        $echeance = '';
        if ($id === '1') {
            $type = "mensuel";
            $prix = "23,99";
            $echeance = '+1 month';
        } else if ($id === '2') {
            $type = "annuel";
            $prix = "259,09";
            $echeance = '+1 year';
        }

        // $token = $this->container->get('security.csrf.token_manager')->getToken('subscribe_form');
        // dd($token);
        $subscriptions = new Subscriptions();
        $form = $this->createForm(SubscribeType::class, $subscriptions);
        $form->handleRequest($request);
        // $form->add('csrf_token', HiddenType::class, [
        //     'data' => $token,
        //     'mapped' => false,
        // ]);

        // if ($form->isSubmitted() && !$this->isCsrfTokenValid('subscribe_form', $request->get('csrf_token'))) {
        //     $this->addFlash('danger', 'Erreur de token CSRF');
        //     return $this->redirectToRoute('payment_user', ['id' => $id]);
        // }

        if ($form->isSubmitted() && $form->isValid()) {
            $activeSubscription = $entityManager->getRepository(Subscriptions::class)->findActiveSubscriptionByUser($user);
            if ($activeSubscription) {
                $this->addFlash('danger', 'Vous avez déjà un abonnement en cours');
                return $this->redirectToRoute('payment_user', ['id' => $id]);
            }
            $subscriptions->setIdUser($user);
            $subscriptions->setDateDebut(new \DateTime());
            $subscriptions->setDateFin((new \DateTime())->modify($echeance));

            $entityManager->persist($subscriptions);

            $roles = $user->getRoles();
            $roles[] = 'ROLE_SUBSCRIBED';
            $user->setRoles($roles);
            $entityManager->persist($user);
            $tokenStorage = $this->container->get('security.token_storage');
            $newToken = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $tokenStorage->setToken($newToken);

            $entityManager->flush();


            return $this->redirectToRoute('profile_user');
        }

        return $this->render('user/payment.html.twig', [
            'type' => $type,
            'prix' => $prix,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/user/profile', name: 'profile_user')]
    public function profile(EntityManagerInterface $entityManager, ReservationController $reservations): Response
    {
        $user = $this->getUser();
        $subscription = $entityManager
            ->getRepository(Subscriptions::class)
            ->findActiveSubscriptionByUser($user);


        $reservations = $entityManager
            ->getRepository(Reservation::class)
            ->findBy(
                ['user' => $user],
                ['RentalStart' => 'DESC']
            );
        // dd($reservations);

        return $this->render('user/profile.html.twig', [
            'subscription' => $subscription,
            'locations' => $reservations
        ]);

    }
    #[Route('/user/update', name: 'profile_update')]
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setFirstName($form->get('firstName')->getData());
            $user->setLastName($form->get('lastName')->getData());
            $user->setAddress($form->get('address')->getData());
            $user->setPostal($form->get('postal')->getData());
            $user->setCity($form->get('city')->getData());
            $user->setTelephone($form->get('telephone')->getData());
            $user->setBirthDate($form->get('birthDate')->getData());
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('profile_user');
        }
        // dd($user);
        return $this->render('user/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
