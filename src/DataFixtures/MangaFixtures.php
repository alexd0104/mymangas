<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Entity\Bibliotheque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MangaFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Pour chaque biblio, on crée ~10 mangas
     * Le titre sera auto-sync via tes callbacks (serie + " — tome X")
     */
    public function load(ObjectManager $manager): void
    {
        $owners = [
            BibliothequeFixtures::REF_BIBLIO_ALEXANDRE,
            BibliothequeFixtures::REF_BIBLIO_QUENTIN,
            BibliothequeFixtures::REF_BIBLIO_JEREMY,
            BibliothequeFixtures::REF_BIBLIO_NICOLAS,
            BibliothequeFixtures::REF_BIBLIO_LAURA,
        ];

        // Quelques séries populaires
        $series = [
            'One Piece', 'Naruto', 'Jujutsu Kaisen', 'Dragon Ball',
            'Death Note', 'L’Attaque des Titans', 'Frieren', 'Demon Slayer',
            'Fullmetal Alchemist', 'Chainsaw Man'
        ];

        foreach ($owners as $refBiblio) {
            /** @var Bibliotheque $biblio */
            $biblio = $this->getReference($refBiblio, Bibliotheque::class);

            // Génère 10 mangas : série tournante + tome = i+1
            for ($i = 0; $i < 10; $i++) {
                $serie = $series[$i % count($series)];
                $tome  = $i + 1;

                $m = new Manga();
                $m->setSerie($serie);
                $m->setTome($tome); // titre recalculé par callbacks
                $m->setBibliotheque($biblio);

                $manager->persist($m);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BibliothequeFixtures::class];
    }
}
