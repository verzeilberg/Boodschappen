<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;

class IndexController extends AbstractActionController {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;
    protected $ps;

    public function __construct(EntityManager $doctrineService, $ps) {
        $this->em = $doctrineService;
        $this->ps = $ps;
    }

    public function indexAction() {

        if (!($user = $this->identity())) {
            // redirect to login page
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $currentDate = new \DateTime();
        $year = (int) $currentDate->format("Y");
        $weekNumber = (int) $currentDate->format("W");
        //Get total of weeknumbers in year (52/53)
        $maxWeekNumber = $this->getIsoWeeksInYear($year);

        //Check if the weekNumber is same as total of weeks in a year
        if ($weekNumber == $maxWeekNumber) {
            $weekNumber = 1; //Next week is weeknr 1
            $year = $year + 1; //Next year
        } else {
            $weekNumber = $weekNumber + 1;
        }

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

            $productList = $this->ps->addFrequentlyOrderedProductsToProductList($productList);
        }


        $qb = $this->em->getRepository('Grocery\Entity\ProductList')->createQueryBuilder('pl');
        $qb->select('pl.year AS year');
        $qb->orderBy('pl.year', 'DESC');
        $qb->groupBy('pl.year');
        $years = $qb->getQuery()->getArrayResult();

        $productLists = $this->em
                ->getRepository('Grocery\Entity\ProductList')
                ->findBy(array(), array('weeknumber' => 'DESC'));



        $settings = $this->em->getRepository('Grocery\Entity\GrocerySettings')->find(1);

        $budgetPerEmployee = $settings->getDefaultBudget();

        $users = $this->em
                ->getRepository('SiteUser\Entity\User')
                ->findAll();

        $totalbudget = (count($users) * $budgetPerEmployee) / 4;
        $totalbudget =  number_format($totalbudget,2,",",".");
        return new ViewModel(
                array(
            'years' => $years,
            'productLists' => $productLists,
            'weekNumber' => $weekNumber,
            'totalbudget' => $totalbudget
        ));
    }

    public function getIsoWeeksInYear($year) {
        $date = new \DateTime;
        $date->setISODate($year, 53);
        return ($date->format("W") === "53" ? 53 : 52);
    }

}
