<?php

namespace App\Controller;

use App\Repository\BibliothequeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Bibliotheque;

class BibliothequeController extends AbstractController
{
    #[Route('/', name: 'bibliotheque_list', methods: ['GET'])]
    public function list(BibliothequeRepository $repo): Response
    {
        $bibliotheques = $repo->findAll();

        return $this->render('bibliotheque/list.html.twig', [
            'bibliotheques' => $bibliotheques,
        ]);
    }

    #[Route('/bibliotheque/{id}', name: 'bibliotheque_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $repo = $doctrine->getRepository(Bibliotheque::class);
        $bibliotheque = $repo->find($id);

        if (!$bibliotheque) {
            throw $this->createNotFoundException('Cette bibliothèque n’existe pas.');
        }

        // On délègue l’affichage à Twig
        return $this->render('bibliotheque/show.html.twig', [
            'bibliotheque' => $bibliotheque,
        ]);
    }
}
