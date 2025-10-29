<?php

namespace App\DataFixtures;

use App\Entity\Vitrine;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VitrineFixtures extends Fixture implements DependentFixtureInterface
{
    // Références publiques (si besoin ailleurs)
    public const REF_VITRINE_PUB_ALEXANDRE = 'vitrine_pub_alexandre';
    public const REF_VITRINE_PUB_QUENTIN = 'vitrine_pub_quentin';
    public const REF_VITRINE_PUB_JEREMY      = 'vitrine_pub_jeremy';
    public const REF_VITRINE_PUB_NICOLAS     = 'vitrine_pub_nicolas';
    public const REF_VITRINE_PUB_LAURA       = 'vitrine_pub_laura';

    public const REF_VITRINE_PRIV_ALEXANDRE = 'vitrine_priv_alexandre';
    public const REF_VITRINE_PRIV_QUENTIN = 'vitrine_priv_quentin';
    public const REF_VITRINE_PRIV_JEREMY      = 'vitrine_priv_jeremy';
    public const REF_VITRINE_PRIV_NICOLAS     = 'vitrine_priv_nicolas';
    public const REF_VITRINE_PRIV_LAURA       = 'vitrine_priv_laura';

    public function load(ObjectManager $manager): void
    {
        // Couple chaque membre à 2 vitrines : publique + privée
        $rows = [
            [MemberFixtures::REF_MEMBER_ALEXANDRE, 'Alexandre'],
            [MemberFixtures::REF_MEMBER_QUENTIN, 'Quentin'],
            [MemberFixtures::REF_MEMBER_JEREMY,      'Jeremy'],
            [MemberFixtures::REF_MEMBER_NICOLAS,     'Nicolas'],
            [MemberFixtures::REF_MEMBER_LAURA,       'Laura'],
        ];

        foreach ($rows as [$refMember, $label]) {
            /** @var Member $member */
            $member = $this->getReference($refMember, Member::class);

            // Vitrine publiée
            $vPub = (new Vitrine())
                ->setDescription('Vitrine publique de ' . $label)
                ->setPubliee(true)
                ->setCreateur($member);

            // Vitrine privée (brouillon)
            $vPriv = (new Vitrine())
                ->setDescription('Vitrine privée de ' . $label)
                ->setPubliee(false)
                ->setCreateur($member);

            $manager->persist($vPub);
            $manager->persist($vPriv);

            // Références — pratiques si d’autres fixtures en auront besoin
            switch ($refMember) {
                case MemberFixtures::REF_MEMBER_ALEXANDRE:
                    $this->addReference(self::REF_VITRINE_PUB_ALEXANDRE,  $vPub);
                    $this->addReference(self::REF_VITRINE_PRIV_ALEXANDRE, $vPriv);
                    break;
                case MemberFixtures::REF_MEMBER_QUENTIN:
                    $this->addReference(self::REF_VITRINE_PUB_QUENTIN,  $vPub);
                    $this->addReference(self::REF_VITRINE_PRIV_QUENTIN, $vPriv);
                    break;
                case MemberFixtures::REF_MEMBER_JEREMY:
                    $this->addReference(self::REF_VITRINE_PUB_JEREMY,  $vPub);
                    $this->addReference(self::REF_VITRINE_PRIV_JEREMY, $vPriv);
                    break;
                case MemberFixtures::REF_MEMBER_NICOLAS:
                    $this->addReference(self::REF_VITRINE_PUB_NICOLAS,  $vPub);
                    $this->addReference(self::REF_VITRINE_PRIV_NICOLAS, $vPriv);
                    break;
                case MemberFixtures::REF_MEMBER_LAURA:
                    $this->addReference(self::REF_VITRINE_PUB_LAURA,  $vPub);
                    $this->addReference(self::REF_VITRINE_PRIV_LAURA, $vPriv);
                    break;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Les membres doivent exister avant de créer leurs vitrines
        return [
            MemberFixtures::class,
        ];
    }
}
