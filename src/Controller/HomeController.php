<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    #[Route('/{locale?fr}', name: 'app_home', requirements: ['locale' => 'fr|en'])]
    public function index(TranslatorInterface $translator, LocaleSwitcher $localeSwitcher, $locale): Response
    {
        $localeSwitcher->setLocale($locale);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'home' => $translator->trans('home'),
            'navbar' => $translator->trans('navbar')
        ]);
    }
}
