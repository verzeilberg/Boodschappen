<?php

namespace Grocery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;

/*
 * Entities
 */
use Grocery\Entity\ProductGroup;

class GroceryDashboardController extends AbstractActionController {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;
    protected $cis;
    protected $vhm;
    protected $ps;
    protected $pls;
    protected $pis;

    public function __construct(EntityManager $doctrineService, $cropImageService, $vhm, $ps, $pls, $pis) {
        $this->em = $doctrineService;
        $this->cis = $cropImageService;
        $this->vhm = $vhm;
        $this->ps = $ps;
        $this->pls = $pls;
        $this->pis = $pis;
    }

    public function indexAction() {
        $this->vhm->get('headScript')->appendFile('/js/loader.js');
        $CurrentDate = new \DateTime();
        $currentYear = $CurrentDate->format('Y');
        $totalCostByWeekByYear = $this->ps->getWeekTotalPriceByYear($currentYear);
        $productsQuantityByYear = $this->ps->getProductsQuantityByYear($currentYear);
        return new ViewModel(
                array(
            'totalCostByWeekByYear' => $totalCostByWeekByYear,
                    'currentYear' => $currentYear,
                )
        );
    }

    public function productAction() {
        $this->vhm->get('headScript')->appendFile('/js/loader.js');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('groceryProduct');
        }
        $product = $this->em
                ->getRepository('Grocery\Entity\Product')
                ->findOneBy(array('productId' => $id));
        if (!$product) {
            return $this->redirect()->toRoute('groceryProduct');
        }

        $years = $this->pls->getProductListYears();

        $quantiyOrderedPRoductByYear = array();
        foreach ($years AS $year) {
            $quantiyOrderedPRoductByYear[$year['year']] = $this->ps->getProductQuantityByYear($id, $year['year']);
        }

        return new ViewModel(
                array(
            'product' => $product,
            'quantiyOrderedProductByYear' => $quantiyOrderedPRoductByYear
                )
        );
    }

}
