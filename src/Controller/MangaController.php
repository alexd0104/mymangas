<?php

namespace App\Controller;

use App\Entity\Manga;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaController extends AbstractController
{
    /**
     * Affiche la fiche d'un Manga
     * URL : /manga/{id}
     */
    #[Route('/manga/{id}', name: 'manga_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $repo = $doctrine->getRepository(Manga::class);
        $manga = $repo->find($id);

        if (!$manga) {
            throw $this->createNotFoundException('Ce manga n’existe pas.');
        }

        return $this->render('manga/show.html.twig', [
            'manga' => $manga,
        ]);
    }
}
