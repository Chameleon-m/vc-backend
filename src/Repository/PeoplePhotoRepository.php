<?php

namespace App\Repository;

use App\Entity\PeoplePhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PeoplePhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeoplePhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeoplePhoto[]    findAll()
 * @method PeoplePhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeoplePhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeoplePhoto::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PeoplePhoto $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(PeoplePhoto $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PeoplePhoto[] Returns an array of PeoplePhoto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PeoplePhoto
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
