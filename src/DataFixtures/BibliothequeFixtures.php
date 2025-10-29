<?php

namespace App\DataFixtures;

use App\Entity\Bibliotheque;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BibliothequeFixtures extends Fixture implements DependentFixtureInterface
{
    public const REF_BIBLIO_ALEXANDRE = 'biblio_alexandre';
    public const REF_BIBLIO_QUENTIN   = 'biblio_quentin';
    public const REF_BIBLIO_JEREMY    = 'biblio_jeremy';
    public const REF_BIBLIO_NICOLAS   = 'biblio_nicolas';
    public const REF_BIBLIO_LAURA     = 'biblio_laura';

    /** @return \Generator<array{refMember:string,label:string,refBiblio:string}> */
    private function map(): \Generator
    {
        yield [MemberFixtures::REF_MEMBER_ALEXANDRE, 'Alexandre', self::REF_BIBLIO_ALEXANDRE];
        yield [MemberFixtures::REF_MEMBER_QUENTIN,   'Quentin',   self::REF_BIBLIO_QUENTIN];
        yield [MemberFixtures::REF_MEMBER_JEREMY,    'Jeremy',    self::REF_BIBLIO_JEREMY];
        yield [MemberFixtures::REF_MEMBER_NICOLAS,   'Nicolas',   self::REF_BIBLIO_NICOLAS];
        yield [MemberFixtures::REF_MEMBER_LAURA,     'Laura',     self::REF_BIBLIO_LAURA];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->map() as [$refMember, $label, $refBiblio]) {
            /** @var Member $member */
            $member = $this->getReference($refMember, Member::class);

            $b = new Bibliotheque();
            $b->setTitre('Bibliothèque de ' . $label);
            $b->setProprietaire($member);
            $member->setBibliotheque($b); // côté inverse si présent

            $manager->persist($b);
            $this->addReference($refBiblio, $b);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [MemberFixtures::class];
    }
}
