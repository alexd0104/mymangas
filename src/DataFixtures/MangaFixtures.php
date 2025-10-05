<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\BibliothequeFixtures;
use App\Entity\Bibliotheque ;



class MangaFixtures extends Fixture 
{
    public function load(ObjectManager $manager): void
    {
        $biblio = $this->getReference(BibliothequeFixtures::REF_BIBLIO_DEMO, Bibliotheque::class);

        $rows = [
            ['One Piece', 'One Piece', 1],
            ['One Piece', 'One Piece', 2],
            ['One Piece', 'One Piece', 3],
            ['Dragon Ball', 'Dragon Ball', 1],
            ['Dragon Ball', 'Dragon Ball', 2],
            ['Dragon Ball', 'Dragon Ball', 3],
            ['Death Note', 'Death Note', 1],
            ['Death Note', 'Death Note', 2],
            ['SNK', 'SNK', 1],
            ['Frieren', 'Frieren', 1],
        ];

        foreach ($rows as [$titre, $serie, $tome]) {
            $m = new Manga();
            $m->setTitre($titre);
            $m->setSerie($serie);
            $m->setTome($tome);
            $m->setBibliotheque($biblio);

            $manager->persist($m);
        }

        $manager->flush();
    }
}
