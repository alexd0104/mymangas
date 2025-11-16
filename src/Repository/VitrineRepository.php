<?php

namespace App\Repository;

use App\Entity\Vitrine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Member;

/**
 * @extends ServiceEntityRepository<Vitrine>
 */
class VitrineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vitrine::class);
    }
/** @return Vitrine[] */
    public function findPublic(): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.publiee = :pub')
            ->setParameter('pub', true)
            ->orderBy('v.id', 'ASC')
            ->getQuery()->getResult();
    }

    /** @return Vitrine[] */
    public function findPrivateByMember(Member $member): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.publiee = :pub')
            ->andWhere('v.createur = :m')
            ->setParameter('pub', false)
            ->setParameter('m', $member)
            ->orderBy('v.id', 'ASC')
            ->getQuery()->getResult();
    }

    /** @return Vitrine[]  (optionnel : tout ce qui est visible pour un membre donné) */
    public function findVisibleFor(?Member $member): array
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.publiee = :pub')
            ->setParameter('pub', true);

        if ($member) {
            // union simple : publiques + privées du membre
            $qb = $this->createQueryBuilder('v')
                ->andWhere('(v.publiee = :pub) OR (v.publiee = :priv AND v.createur = :m)')
                ->setParameter('pub', true)
                ->setParameter('priv', false)
                ->setParameter('m', $member);
        }

        return $qb->orderBy('v.id', 'ASC')->getQuery()->getResult();
    }
}
