<?php

namespace App\Repository;

use App\Entity\TricksPictures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TricksPictures|null find($id, $lockMode = null, $lockVersion = null)
 * @method TricksPictures|null findOneBy(array $criteria, array $orderBy = null)
 * @method TricksPictures[]    findAll()
 * @method TricksPictures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TricksPicturesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TricksPictures::class);
    }

    // /**
    //  * @return TricksPictures[] Returns an array of TricksPictures objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TricksPictures
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
