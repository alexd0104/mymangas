<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberFixtures extends Fixture
{
    public const REF_MEMBER_ALEXANDRE = 'member_alexandre';
    public const REF_MEMBER_QUENTIN   = 'member_quentin';
    public const REF_MEMBER_JEREMY    = 'member_jeremy';
    public const REF_MEMBER_NICOLAS   = 'member_nicolas';
    public const REF_MEMBER_LAURA     = 'member_laura';

    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    /** @return \Generator<array{ref:string,email:string,plain:string}> */
    private function members(): \Generator
    {
        yield ['ref' => self::REF_MEMBER_ALEXANDRE, 'email' => 'alexandre@example.test', 'plain' => '123456'];
        yield ['ref' => self::REF_MEMBER_QUENTIN,   'email' => 'quentin@example.test',   'plain' => '123456'];
        yield ['ref' => self::REF_MEMBER_JEREMY,    'email' => 'jeremy@example.test',    'plain' => '123456'];
        yield ['ref' => self::REF_MEMBER_NICOLAS,   'email' => 'nicolas@example.test',   'plain' => '123456'];
        yield ['ref' => self::REF_MEMBER_LAURA,     'email' => 'laura@example.test',     'plain' => '123456'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->members() as $row) {
            $user = new Member();
            $user->setEmail($row['email']);
            $user->setRoles(['ROLE_USER']);

            $hashed = $this->hasher->hashPassword($user, $row['plain']);
            $user->setPassword($hashed);

            $manager->persist($user);
            $this->addReference($row['ref'], $user);
        }

        $manager->flush();
    }
}
