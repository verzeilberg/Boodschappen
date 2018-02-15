<?php

namespace Grocery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

/*

 * Entities

 */

class GroceryProductListController extends AbstractActionController {

    /**

     * @var \Blog\Service\PostServiceInterface

     */
    protected $em;
    protected $vhm;

    public function __construct(EntityManager $doctrineService, $vhm) {

        $this->em = $doctrineService;

        $this->vhm = $vhm;
    }

    public function indexAction() {

        $this->layout('layout/admin');

        $currentDate = new \DateTime();

        $year = $currentDate->format("Y");

        $weekNumber = $currentDate->format("W");



        $productList = $this->em
                ->getRepository('Grocery\Entity\ProductList')
                ->findOneBy(array('year' => $year, 'weeknumber' => $weekNumber));



        if (!is_object($productList)) {

            $productList = new \Grocery\Entity\ProductList();

            $productList->setYear($year);

            $productList->setWeeknumber($weekNumber);

            $productList->setDateCreated($currentDate);

            $this->em->persist($productList);

            $this->em->flush();
        }

        $qb = $this->em->getRepository('Grocery\Entity\ProductList')->createQueryBuilder('pl');

        $qb->select('pl.year AS year');

        $qb->orderBy('pl.year', 'ASC');

        $qb->groupBy('year');

        $years = $qb->getQuery()->getResult()[0];

        $productLists = $this->em
                ->getRepository('Grocery\Entity\ProductList')
                ->findAll();

        return new ViewModel(
                array(
            'years' => $years,
            'productLists' => $productLists,
            'weekNumber' => $weekNumber
        ));
    }

    public function detailAction() {


        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() == 'admin') {

            $this->layout('layout/admin');
        }



        //$this->vhm->get('headLink')->appendStylesheet('/css/style.css');

        $this->vhm->get('headScript')->appendFile('/js/productList.js');





        $id = (int) $this->params()->fromRoute('id', 0);

        if (empty($id)) {

            return $this->redirect()->toRoute('home');
        }

        $productList = $this->em
                ->getRepository('Grocery\Entity\ProductList')
                ->findOneBy(array('id' => $id));

        if (!$productList) {

            return $this->redirect()->toRoute('home');
        }

        $products = $this->em
                ->getRepository('Grocery\Entity\Product')
                ->findBy(array(), array('name' => 'ASC'));



        $currentDate = new \DateTime();

        $year = $currentDate->format("Y");

        $weekNumber = $currentDate->format("W");



        return new ViewModel(
                array(
            'productList' => $productList,
            'products' => $products,
            'year' => $year,
            'weekNumber' => $weekNumber
                )
        );
    }

}
