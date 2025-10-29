<?php

namespace App\DataFixtures;

use App\Entity\Vitrine;
use App\Entity\Member;
use App\Entity\Manga;
use App\Entity\Bibliotheque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VitrineFixtures extends Fixture implements DependentFixtureInterface
{
    public const REF_VITRINE_PUB_ALEXANDRE = 'vitrine_pub_alexandre';
    public const REF_VITRINE_PUB_QUENTIN   = 'vitrine_pub_quentin';
    public const REF_VITRINE_PUB_JEREMY    = 'vitrine_pub_jeremy';
    public const REF_VITRINE_PUB_NICOLAS   = 'vitrine_pub_nicolas';
    public const REF_VITRINE_PUB_LAURA     = 'vitrine_pub_laura';

    public const REF_VITRINE_PRIV_ALEXANDRE = 'vitrine_priv_alexandre';
    public const REF_VITRINE_PRIV_QUENTIN   = 'vitrine_priv_quentin';
    public const REF_VITRINE_PRIV_JEREMY    = 'vitrine_priv_jeremy';
    public const REF_VITRINE_PRIV_NICOLAS   = 'vitrine_priv_nicolas';
    public const REF_VITRINE_PRIV_LAURA     = 'vitrine_priv_laura';

    /** @return \Generator<array{refMember:string,refBiblio:string,label:string,refPub:string,refPriv:string}> */
    private function map(): \Generator
    {
        yield [
            MemberFixtures::REF_MEMBER_ALEXANDRE,
            BibliothequeFixtures::REF_BIBLIO_ALEXANDRE,
            'Alexandre',
            self::REF_VITRINE_PUB_ALEXANDRE,
            self::REF_VITRINE_PRIV_ALEXANDRE
        ];
        yield [
            MemberFixtures::REF_MEMBER_QUENTIN,
            BibliothequeFixtures::REF_BIBLIO_QUENTIN,
            'Quentin',
            self::REF_VITRINE_PUB_QUENTIN,
            self::REF_VITRINE_PRIV_QUENTIN
        ];
        yield [
            MemberFixtures::REF_MEMBER_JEREMY,
            BibliothequeFixtures::REF_BIBLIO_JEREMY,
            'Jeremy',
            self::REF_VITRINE_PUB_JEREMY,
            self::REF_VITRINE_PRIV_JEREMY
        ];
        yield [
            MemberFixtures::REF_MEMBER_NICOLAS,
            BibliothequeFixtures::REF_BIBLIO_NICOLAS,
            'Nicolas',
            self::REF_VITRINE_PUB_NICOLAS,
            self::REF_VITRINE_PRIV_NICOLAS
        ];
        yield [
            MemberFixtures::REF_MEMBER_LAURA,
            BibliothequeFixtures::REF_BIBLIO_LAURA,
            'Laura',
            self::REF_VITRINE_PUB_LAURA,
            self::REF_VITRINE_PRIV_LAURA
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $mangaRepo = $manager->getRepository(Manga::class);

        foreach ($this->map() as [$refMember, $refBiblio, $label, $refPub, $refPriv]) {
            /** @var Member $member */
            $member = $this->getReference($refMember, Member::class);
            /** @var Bibliotheque $biblio */
            $biblio = $this->getReference($refBiblio, Bibliotheque::class);

            // 2 vitrines
            $vPub = (new Vitrine())
                ->setDescription('Vitrine publique de ' . $label)
                ->setPubliee(true)
                ->setCreateur($member);

            $vPriv = (new Vitrine())
                ->setDescription('Vitrine privée de ' . $label)
                ->setPubliee(false)
                ->setCreateur($member);

            // On prend jusqu’à 8 mangas de la biblio du propriétaire
            $mangas = $mangaRepo->findBy(
                ['bibliotheque' => $biblio],
                ['id' => 'ASC'],
                8
            );

            foreach ($mangas as $i => $m) {
                if ($i % 2 === 0) {
                    // vitrine publique
                    if (method_exists($vPub, 'addManga')) {
                        $vPub->addManga($m);
                    } else {
                        $vPub->getMangas()->add($m);
                    }
                    if (method_exists($m, 'addVitrine')) {
                        $m->addVitrine($vPub);
                    }
                } else {
                    // vitrine privée
                    if (method_exists($vPriv, 'addManga')) {
                        $vPriv->addManga($m);
                    } else {
                        $vPriv->getMangas()->add($m);
                    }
                    if (method_exists($m, 'addVitrine')) {
                        $m->addVitrine($vPriv);
                    }
                }
            }

            $manager->persist($vPub);
            $manager->persist($vPriv);

            $this->addReference($refPub,  $vPub);
            $this->addReference($refPriv, $vPriv);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MemberFixtures::class,
            BibliothequeFixtures::class,
            MangaFixtures::class,
        ];
    }
}
