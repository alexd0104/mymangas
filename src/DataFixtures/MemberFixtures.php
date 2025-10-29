<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberFixtures extends Fixture
{
    public const REF_MEMBER_ALEXANDRE = 'member_alexandre';
    public const REF_MEMBER_QUENTIN = 'member_quentin';
    public const REF_MEMBER_JEREMY      = 'member_jeremy';
    public const REF_MEMBER_NICOLAS     = 'member_nicolas';
    public const REF_MEMBER_LAURA       = 'member_laura';

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    /**
     * Génère les paires [ref, email, password en clair]
     * @return \Generator<array{0:string,1:string,2:string}>
     */
    private function membersGenerator(): \Generator
    {
        yield [self::REF_MEMBER_ALEXANDRE, 'alexandre1@example.test', '123456'];
        yield [self::REF_MEMBER_QUENTIN, 'quentin@example.test', '123456'];
        yield [self::REF_MEMBER_JEREMY,      'jeremy@example.test',     '123456'];
        yield [self::REF_MEMBER_NICOLAS,     'nicolas@example.test',    '123456'];
        yield [self::REF_MEMBER_LAURA,       'laura@example.test',      '123456'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->membersGenerator() as [$ref, $email, $plainPassword]) {
            $user = new Member();
            $user->setEmail($email);

            // Hachage du mot de passe (via le hasher Symfony)
            $hashed = $this->hasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashed);

            // Rôle minimal par défaut
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
            $this->addReference($ref, $user);
        }

        $manager->flush();
    }
}
