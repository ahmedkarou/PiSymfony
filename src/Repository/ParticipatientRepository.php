<?php

namespace App\Repository;

use App\Entity\Participatient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participatient>
 *
 * @method Participatient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participatient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participatient[]    findAll()
 * @method Participatient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participatient::class);
    }
    

//    /**
//     * @return Participatient[] Returns an array of Participatient objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Participatient
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
