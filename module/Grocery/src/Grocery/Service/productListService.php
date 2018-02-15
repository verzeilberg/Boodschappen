<?php

namespace Grocery\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class productListService implements productListServiceInterface {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getProductListYears() {
        $qb = $this->em->getRepository('Grocery\Entity\ProductList')->createQueryBuilder('pl');
        $qb->select('pl.year AS year');
        $qb->orderBy('pl.year', 'ASC');
        $qb->groupBy('pl.year');
        $years = $qb->getQuery()->getArrayResult();

        return $years;
    }

}
