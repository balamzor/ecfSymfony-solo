<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Notation;
use App\Form\BookType;
use App\Form\NotationType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $books = $doctrine->getRepository(Book::class)->findAll();

        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'books' => $books,
        ]);
    }

    #[Route('/book/{id}', name: 'app_book_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, ManagerRegistry $doctrine, int $id, Security $security): Response
    {
        $book = $doctrine->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Mauvaise route, ce livre n\'existe pas!');
        }

        $notations = $doctrine->getRepository(Notation::class)->findBy(['idBook' => $book]);
        // dd($notations);

        $notation = new Notation();
        $notation->setIdBook($book)
            ->setUserId($security->getUser())
            ->setDate(new \DateTime());

        $form = $this->createForm(NotationType::class, $notation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($notation);
            $entityManager->flush();
            return $this->redirectToRoute('app_book_detail', ['id' => $id]);
        }

        return $this->render('book/detail.html.twig', [
            'book' => $book,
            'notations' => $notations,
            'form' => $form->createView(),
        ]);
    }
}

