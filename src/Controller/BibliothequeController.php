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

    #[Route('/bibliotheque/{id}', name: 'bibliotheque_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $repo = $doctrine->getRepository(Bibliotheque::class);
        $bibliotheque = $repo->find($id);

        if (!$bibliotheque) {
            throw $this->createNotFoundException('Cette bibliothèque n’existe pas.');
        }

        // Maquettage HTML "brut" (sans Twig, pour l’instant)
        $titre = htmlspecialchars($bibliotheque->getTitre(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $html = '<html><body>';
        $html .= '<h1>Fiche Bibliothèque</h1>';
        $html .= '<p><strong>Titre :</strong> ' . $titre . '</p>';

        // Si tu as déjà la relation OneToMany vers les mangas :
        if (method_exists($bibliotheque, 'getMangas')) {
            $mangas = $bibliotheque->getMangas();
            $html .= '<h2>Mangas dans cette bibliothèque</h2>';
            if (count($mangas) === 0) {
                $html .= '<p>Aucun manga pour le moment.</p>';
            } else {
                $html .= '<ul>';
                foreach ($mangas as $m) {
                    // on reste prudent si les getters varient
                    $mt = method_exists($m, 'getTitre') ? htmlspecialchars((string)$m->getTitre(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : 'Sans titre';
                    $ms = method_exists($m, 'getSerie') ? htmlspecialchars((string)$m->getSerie(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';
                    $mtome = method_exists($m, 'getTome') ? $m->getTome() : null;

                    $ligne = $mt;
                    if ($ms) { $ligne .= ' — ' . $ms; }
                    if ($mtome) { $ligne .= ' (tome ' . (int)$mtome . ')'; }

                    $html .= '<li>' . $ligne . '</li>';
                }
                $html .= '</ul>';
            }
        }

        // Lien retour vers la liste sur "/"
        $html .= '<p><a href="' . $this->generateUrl('bibliotheque_list') . '">← Retour à la liste</a></p>';
        $html .= '</body></html>';

        return new Response($html);
    }
}
