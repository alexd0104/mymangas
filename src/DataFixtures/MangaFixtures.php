<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Entity\Bibliotheque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MangaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Pool de "bons mangas" (on varie les séries)
        // Format: [titre, serie, tome]
        $pool = [
            ['One Piece',            'One Piece',            1],
            ['Naruto',               'Naruto',               1],
            ['Bleach',               'Bleach',               1],
            ['Dragon Ball',          'Dragon Ball',          1],
            ['Fullmetal Alchemist',  'Fullmetal Alchemist',  1],
            ['Attack on Titan',      'Attack on Titan',      1], // SNK
            ['Demon Slayer',         'Demon Slayer',         1],
            ['Jujutsu Kaisen',       'Jujutsu Kaisen',       1],
            ['My Hero Academia',     'My Hero Academia',     1],
            ['Chainsaw Man',         'Chainsaw Man',         1],
            ['Vinland Saga',         'Vinland Saga',         1],
            ['Spy x Family',         'Spy x Family',         1],
            ['Tokyo Ghoul',          'Tokyo Ghoul',          1],
            ['Haikyuu!!',            'Haikyuu!!',            1],
            ['Frieren',              'Frieren',              1],
        ];

        // Références (depuis BibliothequeFixtures)
        $libRefs = [
            BibliothequeFixtures::REF_BIBLIO_ALEXANDRE,
            BibliothequeFixtures::REF_BIBLIO_QUENTIN,
            BibliothequeFixtures::REF_BIBLIO_JEREMY,
            BibliothequeFixtures::REF_BIBLIO_NICOLAS,
            BibliothequeFixtures::REF_BIBLIO_LAURA,
        ];

        // 10 mangas par bibliothèque, en prenant des tranches décalées du pool pour varier
        $perLib = 10;
        $poolCount = count($pool);

        foreach ($libRefs as $i => $refName) {
            /** @var Bibliotheque $biblio */
            $biblio = $this->getReference($refName, Bibliotheque::class);

            for ($k = 0; $k < $perLib; $k++) {
                // Décalage pour varier les séries selon la bibliothèque
                $index = ($i * 3 + $k) % $poolCount;
                [$titre, $serie, $tome] = $pool[$index];
                $tome = random_int(1, 10);


                $m = new Manga();
                $m->setTitre($titre);
                $m->setSerie($serie);
                $m->setTome($tome);
                $m->setBibliotheque($biblio);

                $manager->persist($m);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Assure que BibliothequeFixtures est chargée avant celle-ci
        return [
            BibliothequeFixtures::class,
        ];
    }
}
