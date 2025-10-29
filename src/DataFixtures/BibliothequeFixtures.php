<?php
namespace App\DataFixtures;

use App\Entity\Bibliotheque;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BibliothequeFixtures extends Fixture implements DependentFixtureInterface
{
    public const REF_BIBLIO_DEMO       = 'biblio_demo';
    public const REF_BIBLIO_ALEXANDRE = 'biblio_alexandre';
    public const REF_BIBLIO_QUENTIN = 'biblio_quentin';
    public const REF_BIBLIO_JEREMY      = 'biblio_jeremy';
    public const REF_BIBLIO_NICOLAS     = 'biblio_nicolas';
    public const REF_BIBLIO_LAURA       = 'biblio_laura';

    public function load(ObjectManager $manager): void
    {
        $map = [
            ['Alexandre', self::REF_BIBLIO_ALEXANDRE, MemberFixtures::REF_MEMBER_ALEXANDRE],
            ['Alexandre', self::REF_BIBLIO_QUENTIN, MemberFixtures::REF_MEMBER_QUENTIN],
            ['Jeremy',    self::REF_BIBLIO_JEREMY,      MemberFixtures::REF_MEMBER_JEREMY],
            ['Nicolas',   self::REF_BIBLIO_NICOLAS,     MemberFixtures::REF_MEMBER_NICOLAS],
            ['Laura',     self::REF_BIBLIO_LAURA,       MemberFixtures::REF_MEMBER_LAURA],
        ];

        $firstBiblio = null;

        foreach ($map as [$prenom, $refBiblio, $refMember]) {
            /** @var Member $member */
            $member = $this->getReference($refMember, Member::class);

            $b = new Bibliotheque();
            $b->setTitre('Bibliothèque de ' . $prenom);
            // Lier AVANT le flush (respect du NOT NULL)
            $b->setProprietaire($member);
            $member->setBibliotheque($b);

            $manager->persist($b);
            $this->addReference($refBiblio, $b);

            $firstBiblio ??= $b;
        }

        $manager->flush();

        // compat: REF_BIBLIO_DEMO pointe sur la 1re
        if ($firstBiblio) {
            $this->addReference(self::REF_BIBLIO_DEMO, $firstBiblio);
        }
    }

    public function getDependencies(): array
    {
        return [MemberFixtures::class];
    }
}
