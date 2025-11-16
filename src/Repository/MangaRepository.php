<?php

namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Member;

/**
 * @extends ServiceEntityRepository<Manga>
 */
class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }

   /**
     * @return Manga[] Mangas appartenant au membre via sa bibliothèque
     */
    public function findMemberMangas(Member $member): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.bibliotheque', 'b')
            ->andWhere('b.proprietaire = :member')
            ->setParameter('member', $member)
            ->orderBy('m.id', 'ASC')
            ->getQuery()->getResult();
    }
}