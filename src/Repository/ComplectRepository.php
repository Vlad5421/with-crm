<?php

namespace App\Repository;

use App\Entity\Complect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Complect|null find($id, $lockMode = null, $lockVersion = null)
 * @method Complect|null findOneBy(array $criteria, array $orderBy = null)
 * @method Complect[]    findAll()
 * @method Complect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComplectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complect::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Complect $entity, bool $flush = true): void
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
    public function remove(Complect $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
    * @return Complect[] Returns an array of Complect objects
    */
    public function findAllByFilial($value)
    {
        return $this->createQueryBuilder('complect')
            ->andWhere('complect.filial = :val')
            ->setParameter('val', $value)
            ->leftJoin('complect.service', 'service')
            ->addSelect('service')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Complect[] Returns an array of Complect objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


}
