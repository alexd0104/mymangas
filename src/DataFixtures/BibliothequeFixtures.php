<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Bibliotheque;


class BibliothequeFixtures extends Fixture
{

    public const REF_BIBLIO_DEMO = 'biblio_demo';

    public function load(ObjectManager $manager): void
    {
        $biblio = new Bibliotheque();
        $biblio->setTitre('Bibliothèque de Démo');

        $manager->persist($biblio);
        $manager->flush();

        $this->addReference(self::REF_BIBLIO_DEMO, $biblio);


    }
    
}
