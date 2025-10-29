<?php

namespace App\Controller;

use App\Entity\Vitrine;
use App\Form\VitrineType;
use App\Repository\VitrineRepository;
use App\Entity\Manga;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/vitrine')]
final class VitrineController extends AbstractController
{
    #[Route(name: 'app_vitrine_index', methods: ['GET'])]
    public function index(VitrineRepository $repo): Response
    {
        // Montre TOUTES les vitrines dans la liste (publiques + privées)
        // La protection reste dans show() qui renverra 403 si nécessaire.
        $vitrines = $repo->findBy([], ['id' => 'ASC']);

        return $this->render('vitrine/index.html.twig', [
            'vitrines' => $vitrines,
        ]);
    }


    #[Route('/new', name: 'app_vitrine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vitrine = new Vitrine();
        $form = $this->createForm(VitrineType::class, $vitrine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vitrine);
            $entityManager->flush();

            return $this->redirectToRoute('app_vitrine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vitrine/new.html.twig', [
            'vitrine' => $vitrine,
            'form' => $form,
        ]);
    }

   #[Route('/{id}', name: 'app_vitrine_show', methods: ['GET'])]
    public function show(Vitrine $vitrine): Response
    {
        $user = $this->getUser(); // Member|UserInterface|null
        $isOwner = $user instanceof \App\Entity\Member && $vitrine->getCreateur() === $user;
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if (!$vitrine->isPubliee() && !$isOwner && !$isAdmin) {
            throw $this->createAccessDeniedException("Cette vitrine est privée.");
        }

        return $this->render('vitrine/show.html.twig', [
            'vitrine' => $vitrine,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vitrine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vitrine $vitrine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VitrineType::class, $vitrine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vitrine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vitrine/edit.html.twig', [
            'vitrine' => $vitrine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vitrine_delete', methods: ['POST'])]
    public function delete(Request $request, Vitrine $vitrine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vitrine->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vitrine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vitrine_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affichage public d'un Manga dans le contexte d'une Vitrine.
     * URL finale: /vitrine/{vitrine_id}/manga/{manga_id}
     */
    #[Route(
        '/{vitrine_id}/manga/{manga_id}',
        name: 'app_vitrine_manga_show',
        requirements: ['vitrine_id' => '\d+', 'manga_id' => '\d+'],
        methods: ['GET']
    )]
    public function mangaShow(
        #[MapEntity(id: 'vitrine_id')] Vitrine $vitrine,
        #[MapEntity(id: 'manga_id')] Manga $manga
    ): Response {
        // 1) la vitrine doit contenir ce manga
        if (!$vitrine->getMangas()->contains($manga)) {
            throw $this->createNotFoundException("Ce manga n'appartient pas à cette vitrine.");
        }

        // 2) la vitrine doit être publiée (sinon accès refusé)
        if (!$vitrine->isPubliee()) {
            throw $this->createAccessDeniedException("Cette vitrine n'est pas publique.");
        }

        return $this->render('vitrine/mangashow.html.twig', [
            'manga'   => $manga,
            'vitrine' => $vitrine,
        ]);
    }
}
