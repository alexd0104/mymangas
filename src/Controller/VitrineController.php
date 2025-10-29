<?php

namespace App\Controller;

use App\Entity\Vitrine;
use App\Form\VitrineType;
use App\Repository\VitrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Manga;
use Doctrine\Persistence\ManagerRegistry;


#[Route('/vitrine')]
final class VitrineController extends AbstractController
{
    #[Route(name: 'app_vitrine_index', methods: ['GET'])]
    public function index(VitrineRepository $vitrineRepository): Response
    {
        return $this->render('vitrine/index.html.twig', [
            'vitrines' => $vitrineRepository->findAll(),
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

    #[Route('/vitrine/manga/{id}', name: 'app_vitrine_manga_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function mangaShow(ManagerRegistry $doctrine, int $id): Response
    {
        $manga = $doctrine->getRepository(Manga::class)->find($id);
        if (!$manga) {
            throw $this->createNotFoundException('Ce manga n’existe pas.');
        }

        return $this->render('vitrine/mangashow.html.twig', [
            'manga' => $manga,
        ]);
    }
}
