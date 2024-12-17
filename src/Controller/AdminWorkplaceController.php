<?php

namespace App\Controller;

use App\Entity\Workspace;
use App\Form\WorkspaceType;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/workplace')]
final class AdminWorkplaceController extends AbstractController
{
    #[Route(name: 'app_admin_workplace_index', methods: ['GET'])]
    public function index(WorkspaceRepository $workspaceRepository): Response
    {
        return $this->render('admin_workplace/index.html.twig', [
            'workspaces' => $workspaceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_workplace_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workspace = new Workspace();
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory') . '/workspaces', $fileName);

                $workspace->setImage($fileName);
            }

            $newEquipments = $form->get('equipment')->getData();

            foreach ($newEquipments as $equip) {
                $workspace->addEquipment($equip);
            }

            $entityManager->persist($workspace);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_workplace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_workplace/new.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_workplace_show', methods: ['GET'])]
    public function show(Workspace $workspace): Response
    {
        return $this->render('admin_workplace/show.html.twig', [
            'workspace' => $workspace,
        ]);
    }

    // src/Controller/AdminWorkplaceController.php
    #[Route('/{id}/edit', name: 'app_admin_workplace_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Workspace $workspace, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image (si nouvelle image)
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory') . '/workspaces', $fileName);
                $workspace->setImage($fileName);
            }

            // Ajouter et supprimer les équipements
            $newEquipments = $form->get('equipment')->getData();

            // Supprimer les équipements qui ne sont plus sélectionnés
            foreach ($workspace->getEquipment() as $equip) {
                if (!$newEquipments->contains($equip)) {
                    $workspace->removeEquipment($equip);
                }
            }

            // Ajouter les nouveaux équipements
            foreach ($newEquipments as $equip) {
                if (!$workspace->getEquipment()->contains($equip)) {
                    $workspace->addEquipment($equip);
                }
            }

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Redirection après la modification
            return $this->redirectToRoute('app_admin_workplace_index');
        }

        return $this->render('admin_workplace/edit.html.twig', [
            'workspace' => $workspace,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_admin_workplace_delete', methods: ['POST'])]
    public function delete(Request $request, Workspace $workspace, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workspace->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($workspace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_workplace_index', [], Response::HTTP_SEE_OTHER);
    }
}
