<?php

namespace App\Repository;

use App\Entity\People;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method People|null find($id, $lockMode = null, $lockVersion = null)
 * @method People|null findOneBy(array $criteria, array $orderBy = null)
 * @method People[]    findAll()
 * @method People[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeopleRepository extends ServiceEntityRepository
{
    private const DAYS_BEFORE_REJECTED_REMOVAL = 7;

    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, People::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(People $entity, bool $flush = true): void
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
    public function remove(People $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findStatePublished(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.state = :state')
            ->setParameter('state', 'published')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function countOldRejected(): int
    {
        return $this->getOldRejectedQueryBuilder()->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
    }

    public function deleteOldRejected(): int
    {
        return $this->getOldRejectedQueryBuilder()->delete()->getQuery()->execute();
    }

    private function getOldRejectedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.state = :state_rejected or p.state = :state_spam')
            ->andWhere('p.createdAt < :date')
            ->setParameters([
                'state_rejected' => 'rejected',
                'state_spam' => 'spam',
                'date' => new \DateTimeImmutable(-self::DAYS_BEFORE_REJECTED_REMOVAL . ' days'),
            ]);
    }
}
