<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    /**
     * @return int Returns an array of Tricks objects
     */
    public function countAll($trick, $filterGroup = null)
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where("c.trick = :trick")
            ->setParameter("trick", $trick)
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

    /**
    * @return Comments[] Returns an array of Comments objects
    */
    public function findByLimit($trick, $start, $max)
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.created_at', 'DESC')
            ->where("c.trick = :trick")
            ->setParameter("trick", $trick)
            ->setFirstResult($start)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Comments
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
