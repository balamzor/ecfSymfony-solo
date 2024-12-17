<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Workspace;

class WorkspacesController extends AbstractController
{
    #[Route('/workspaces', name: 'app_workspaces')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $workspaces = $entityManager->getRepository(Workspace::class)->findAll();

        return $this->render('workspaces/index.html.twig', [
            'workspaces' => $workspaces,
        ]);
    }

    #[Route('/workspace/{id}', name: 'workspace_detail')]
    public function detail(EntityManagerInterface $entityManager, int $id): Response
    {
        $workspace = $entityManager->getRepository(Workspace::class)->find($id);

        if (!$workspace) {
            throw $this->createNotFoundException('Salle de travail non trouvée.');
        }

        return $this->render('workspaces/detail.html.twig', [
            'workspace' => $workspace,
        ]);
    }
}
