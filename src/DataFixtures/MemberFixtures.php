<?php
namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MemberFixtures extends Fixture
{
    public const REF_MEMBER_ALEXANDRE = 'member_alexandre';
    public const REF_MEMBER_QUENTIN = 'member_quentin';
    public const REF_MEMBER_JEREMY      = 'member_jeremy';
    public const REF_MEMBER_NICOLAS     = 'member_nicolas';
    public const REF_MEMBER_LAURA       = 'member_laura';

    public function load(ObjectManager $manager): void
    {
        $rows = [
            [self::REF_MEMBER_ALEXANDRE, 'alexandre@example.test'],
            [self::REF_MEMBER_QUENTIN, 'quentin@example.test'],
            [self::REF_MEMBER_JEREMY,      'jeremy@example.test'],
            [self::REF_MEMBER_NICOLAS,     'nicolas@example.test'],
            [self::REF_MEMBER_LAURA,       'laura@example.test'],
        ];

        foreach ($rows as [$ref, $email]) {
            $m = new Member();
            $m->setEmail($email);
            $m->setPassword(''); // temporaire; on hashsera quand on mettra l’auth
            $m->setRoles(['ROLE_USER']);
            $manager->persist($m);
            $this->addReference($ref, $m);
        }

        $manager->flush();
    }
}
