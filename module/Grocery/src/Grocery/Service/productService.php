<?php

namespace Grocery\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class productService implements productServiceInterface {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getProductQuantityByYear($productId = NULL, $year = NULL) {
        $qb = $this->em->getRepository('Grocery\Entity\ProductListDetail')->createQueryBuilder('pld');
        $qb->join('pld.productList', 'pl');
        $qb->where('pld.productId = ' . $productId);
        $qb->andWhere('pl.year = ' . $year);
        $productsQuantity = $qb->getQuery()->getResult();

        $totalOrdered = 0;
        foreach ($productsQuantity as $quantity) {
            $totalOrdered = $totalOrdered + $quantity->getQuantity();
        }

        return $totalOrdered;
    }

    public function getProductsQuantityByYear($year = NULL) {
        $qb = $this->em->getRepository('Grocery\Entity\Product')->createQueryBuilder('p');
        $qb->join('p.productListDetails', 'pld');
        $qb->join('pld.productList', 'pl');
        $qb->where('pl.year = ' . $year);
        $productsQuantity = $qb->getQuery()->getResult();


//        var_dump($productsQuantity);
//        die;
//        $totalOrdered = 0;
//        foreach ($productsQuantity as $quantity) {
//            $totalOrdered = $totalOrdered + $quantity->getQuantity();
//        }
//
//        return $totalOrdered;
    }

    public function getWeekTotalPriceByYear($year = NULL) {
        $productLists = $this->em
                ->getRepository('Grocery\Entity\ProductList')
                ->findBy(array('year' => $year), array('weeknumber' => 'ASC'));


        $totalCostByWeekByYear = array();
        foreach ($productLists AS $productList) {
            $totalCostByWeekByYear[$productList->getWeeknumber()] = $productList->getTotalCost();
        }

        return $totalCostByWeekByYear;
    }

    public function addFrequentlyOrderedProductsToProductList($productList = NULL) {
        $qb = $this->em->getRepository('Grocery\Entity\Product')->createQueryBuilder('p');
        $qb->where('p.orderFrequenty IS NOT NULL');
        $products = $qb->getQuery()->getResult();

        foreach ($products AS $product) {
            $productListDetail = $this->em
                    ->getRepository('Grocery\Entity\ProductListDetail')
                    ->findOneBy(array('productId' => $product->getProductId()), array('productListId' => 'ASC'));

            if (empty($productListDetail)) {
                $productListDetail = new \Grocery\Entity\ProductListDetail();
                $productListDetail->setProduct($product);
                $productListDetail->setProductList($productList);
                $productListDetail->setQuantity(1);
                $this->em->persist($productListDetail);
                $this->em->flush();
            } else {
                if ($productListDetail->getProductList()->getId() < $productList->getId()) {
                    $weekNr = $productListDetail->getProductList()->getWeeknumber();
                    $year = $productListDetail->getProductList()->getYear();

                    $week_start = new \DateTime();
                    $week_start->setISODate($year, $weekNr);

                    $week_start2 = new \DateTime();
                    $week_start2->setISODate($productList->getYear(), $productList->getWeeknumber());
                    $interval = $week_start->diff($week_start2);
                    $lastTimeOrderedInDays = $interval->format('%a');
                    $orderFreguencyInDays = $this->getOrderFrequencyInDays($product->getOrderFrequenty());
                    if ($orderFreguencyInDays <= $lastTimeOrderedInDays) {
                        $productListDetail = new \Grocery\Entity\ProductListDetail();
                        $productListDetail->setProduct($product);
                        $productListDetail->setProductList($productList);
                        $productListDetail->setQuantity(1);
                        $this->em->persist($productListDetail);
                        $this->em->flush();
                    }
                }
            }
        }

        return $productList;
    }

    public function getOrderFrequencyInDays($orderFreqentie) {

        switch ($orderFreqentie) {
            case 'Dagelijks':
                return 1;
            case 'Weekelijks':
                return 7;
            case 'Maandelijks':
                return 30;
        }
    }

    public function getCleanProductName($name = NULL) {
        if ($name != NULL) {
            return preg_replace('/[^A-Za-z0-9- éäöëâ+\-]/', '', $name);
        } else {
            return;
        }
    }

    public function getFirstCharacterOfString($string = NULL) {

        if ($string != NULL) {
            $productNameArray = str_split($string);
            foreach ($productNameArray AS $character) {
                if (preg_match('/[a-zA-Z0-9]/', $character)) {
                    return strtoupper($character);
                }
            }
        } else {
            return;
        }
    }

    public function returnAHJsonArrayBAsedOnLink($externalUrl) {

        $productArray = '';

        if (!empty($externalUrl)) {
            $stringURL = trim($externalUrl, "/");

            $urlParts = explode("/", $stringURL);

            $arrayLength = count($urlParts);

            $firstItem = $urlParts[2];

            $lastItem = $arrayLength - 1;

            $oneButLast = $arrayLength - 2;

            $delegateURL = 'https://www.ah.nl/service/rest/delegate?url=%2Fproducten%2Fproduct%2F' . $urlParts[$oneButLast] . '%2F' . $urlParts[$lastItem];

            $html = file_get_contents($delegateURL);

            $productArray = json_decode($html, true);
        }

        return $productArray;
    }

}
