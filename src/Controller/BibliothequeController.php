<?php

namespace App\Controller;

use App\Repository\BibliothequeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BibliothequeController extends AbstractController
{
    #[Route('/', name: 'bibliotheque_list', methods: ['GET'])]
    public function list(BibliothequeRepository $repo): Response
    {
        // Charge toutes les bibliothèques (inventaires)
        $bibliotheques = $repo->findAll();

        // Fabrication d'un petit HTML "maquette" (sans Twig pour l'instant)
        $html = "<html><body>";
        $html .= "<h1>Liste des bibliothèques (inventaires)</h1>";

        if (!$bibliotheques) {
            $html .= "<p>Aucune bibliothèque pour le moment.</p>";
        } else {
            $html .= "<ul>";
            foreach ($bibliotheques as $b) {
                // Sécuriser l'affichage basique
                $titre = htmlspecialchars($b->getTitre(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $html .= "<li>{$titre} (id: " . $b->getId() . ")</li>";
            }
            $html .= "</ul>";
        }

        $html .= "</body></html>";

        return new Response($html);
    }
}
