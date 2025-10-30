<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Form\MangaType;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Bibliotheque;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;


#[Route('/manga')]
final class MangaController extends AbstractController
{
    #[Route(name: 'app_manga_index', methods: ['GET'])]
    public function index(MangaRepository $mangaRepository): Response
    {
        return $this->render('manga/index.html.twig', [
            'mangas' => $mangaRepository->findAll(),
        ]);
    }

    
#[Route('/manga/new/{id}', name: 'app_manga_new', methods: ['GET', 'POST'])]
public function new(
        Request $request,
        EntityManagerInterface $em,
        #[MapEntity(id: 'id')] Bibliotheque $bibliotheque
    ): Response {
        $user = $this->getUser();
        $isOwner = $user instanceof \App\Entity\Member && $bibliotheque->getProprietaire() === $user;
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if (!$isOwner && !$isAdmin) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas ajouter de manga dans cette bibliothèque.");
        }

        $manga = new Manga();
        $manga->setBibliotheque($bibliotheque);

        $form = $this->createForm(\App\Form\MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($manga);
            $em->flush();

            return $this->redirectToRoute('bibliotheque_show', [
                'id' => $bibliotheque->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('manga/new.html.twig', [
            'manga' => $manga,
            'form' => $form,
            'bibliotheque' => $bibliotheque,
        ]);
    }

    #[Route('/{id}', name: 'app_manga_show', methods: ['GET'])]
    public function show(Manga $manga): Response
    {
        return $this->render('manga/show.html.twig', [
            'manga' => $manga,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_manga_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('bibliotheque_show', [
                'id' => $manga->getBibliotheque()->getId()
                ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('manga/edit.html.twig', [
            'manga' => $manga,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_manga_delete', methods: ['POST'])]
    public function delete(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$manga->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($manga);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_manga_index', [], Response::HTTP_SEE_OTHER);
    }
}
