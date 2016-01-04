<?php

namespace Site\MainBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class NewsRepository extends EntityRepository
{

    /**
     * Поиск всех новостей
     *
     * @return array
     */
    public function findAll(){
        return $this->createQueryBuilder('n')
            ->orderBy('n.date', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск последних новостей
     *
     * @param int $number
     * @return array
     */
    public function findLastAll($number = 1){
        if($number <= 0){
            return false;
        }

        return $this->createQueryBuilder('n')
            ->orderBy('n.date', 'desc')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
    }
}
