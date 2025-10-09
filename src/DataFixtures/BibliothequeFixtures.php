<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Bibliotheque;

class BibliothequeFixtures extends Fixture
{
    // Compatibilité avec l'existant : pointe vers "Bibliothèque de Alexandre" (la 1ʳᵉ)
    public const REF_BIBLIO_DEMO = 'biblio_demo';

    // Références explicites (utiles depuis d'autres fixtures)
    public const REF_BIBLIO_ALEXANDRE = 'biblio_alexandre';
    public const REF_BIBLIO_QUENTIN = 'biblio_quentin';
    public const REF_BIBLIO_JEREMY      = 'biblio_jeremy';
    public const REF_BIBLIO_NICOLAS     = 'biblio_nicolas';
    public const REF_BIBLIO_LAURA       = 'biblio_laura';

    public function load(ObjectManager $manager): void
    {
        $prenoms = ['Alexandre', 'Alexandre', 'Jeremy', 'Nicolas', 'Laura'];
        $refs    = [
            self::REF_BIBLIO_ALEXANDRE,
            self::REF_BIBLIO_QUENTIN,
            self::REF_BIBLIO_JEREMY,
            self::REF_BIBLIO_NICOLAS,
            self::REF_BIBLIO_LAURA,
        ];

        $first = null;

        foreach ($prenoms as $i => $prenom) {
            $biblio = new Bibliotheque();
            $biblio->setTitre('Bibliothèque de ' . $prenom);

            $manager->persist($biblio);

            // Ajouter des références utilisables par d'autres fixtures
            $this->addReference($refs[$i], $biblio);

            if ($i === 0) {
                $first = $biblio; // "Bibliothèque de Alexandre" (1ʳᵉ)
            }
        }

        $manager->flush();

        // Référence legacy pour compatibilité (ex: utilisée par MangaFixtures)
        if ($first) {
            $this->addReference(self::REF_BIBLIO_DEMO, $first);
        }
    }
}
