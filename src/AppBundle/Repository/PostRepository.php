<?php

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    public function getAllOrdered()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.date', 'DESC');
        
        return $qb->getQuery()->getResult();
    }
    
    public function getTotalCount()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}