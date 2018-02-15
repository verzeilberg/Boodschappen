<?php

namespace Grocery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;

/*
 * Entities
 */
use Grocery\Entity\ProductListDetail;
use Grocery\Entity\GroceryLog;

class GroceryProductListAjaxController extends AbstractActionController {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;
    protected $vhm;

    public function __construct(EntityManager $doctrineService, $vhm) {
        $this->em = $doctrineService;
        $this->vhm = $vhm;
    }

    public function searchProductsAction() {
        $products = '';
        if ($this->getRequest()->isPost()) {
            $searchTerm = $this->params()->fromPost('searchString');

            if (!empty($searchTerm)) {
                $qb = $this->em->getRepository('Grocery\Entity\Product')->createQueryBuilder('p');
                $orX = $qb->expr()->orX();
                $orX->add($qb->expr()->like('p.name', $qb->expr()->literal("%$searchTerm%")));
                $orX->add($qb->expr()->like('p.productNumber', $qb->expr()->literal("%$searchTerm%")));
                $qb->where($orX);
                $qb->orderBy('p.name', 'ASC');
                $products = $qb->getQuery()->getResult();

                $productsArray = array();
                if (count($products) > 0) {
                    $succes = true;
                    foreach ($products AS $product) {
                        $images = $product->getProductImages();
                        $image = '';
                        foreach ($images AS $image) {
                            foreach ($image->getProductImageTypes('150x150') AS $imageType) {
                                $image = '<img class="img-responsive" src="/' . $imageType->getFolder() . $imageType->getFileName() . '" alt="' . $product->getName() . '" width="75" height="auto" />';
                            }
                            break;
                        }
                        $productsArray[] = array('id' => $product->getProductId(), 'image' => $image, 'naam' => $product->getName());
                    }
                } else {
                    $succes = false;
                }
            } else {
                $succes = false;
            }
        }

        $ajaxURL = '/productListAjax/addProductToProductList';
        return new JsonModel([
            'succes' => $succes,
            'ajaxURL' => $ajaxURL,
            'products' => $productsArray
        ]);
    }

    public function addProductToProductListAction() {
        $succes = true;
        if ($this->getRequest()->isPost()) {
            $productID = $this->params()->fromPost('productID');
            $productListID = $this->params()->fromPost('productListID');
            $quantity = 1;


            if (!empty($productListID) && !empty($productID)) {
                $product = $this->em
                        ->getRepository('Grocery\Entity\Product')
                        ->findOneBy(array('productId' => $productID));

                $productList = $this->em
                        ->getRepository('Grocery\Entity\ProductList')
                        ->findOneBy(array('id' => $productListID));
                if (!empty($product) && !empty($productList)) {
                    $qb = $this->em->getRepository('Grocery\Entity\ProductListDetail')->createQueryBuilder('pld');
                    $qb->where('pld.productId = ' . $product->getProductId());
                    $qb->andWhere('pld.productListId = ' . $productList->getId());
                    $qb->andWhere('pld.deleted = false');
                    $productListDetail = $qb->getQuery()->getOneOrNullResult();

                    if (!empty($productListDetail)) {

                        //Update product list detail
                        $quantityProductListDetail = $productListDetail->getQuantity();
                        $newQuantityProductListDetail = $quantityProductListDetail + (int) $quantity;
                        $productListDetail->setQuantity($newQuantityProductListDetail);
                        $this->em->persist($productListDetail);

                        //Create grocery log
                        $groceryLog = new GroceryLog();
                        $groceryLog->setCrudType('U');
                        $groceryLog->setDateTime(new \DateTime());
                        $groceryLog->setUser($this->identity());
                        $groceryLog->setProductListDetail($productListDetail);
                        $this->em->persist($groceryLog);

                        $this->em->flush();
                        $succes = 'addQuantity';
                    } else {
                        //Create product list detail
                        $productListDetail = new ProductListDetail();
                        $productListDetail->setProduct($product);
                        $productListDetail->setProductList($productList);
                        $productListDetail->setQuantity($quantity);
                        $this->em->persist($productListDetail);

                        //Create grocery log
                        $groceryLog = new GroceryLog();
                        $groceryLog->setCrudType('C');
                        $groceryLog->setDateTime(new \DateTime());
                        $groceryLog->setUser($this->identity());
                        $groceryLog->setProductListDetail($productListDetail);
                        $this->em->persist($groceryLog);

                        $this->em->flush();
                    }

                    $images = $product->getProductImages();
                    $image = '';
                    foreach ($images AS $image) {
                        foreach ($image->getProductImageTypes('150x150') AS $imageType) {
                            $image = '<img class="img-responsive" src="/' . $imageType->getFolder() . $imageType->getFileName() . '" alt="' . $product->getName() . '" width="75" height="auto" />';
                        }
                        break;
                    }
                    $returnArray = array('id' => $product->getProductId(), 'image' => $image, 'naam' => $product->getName(), 'productListDetailID' => $productListDetail->getId(), 'quantity' => $productListDetail->getQuantity());
                } else {
                    $succes = false;
                }
            } else {
                $succes = false;
            }
        } else {
            $succes = false;
        }



        return new JsonModel([
            'succes' => $succes,
            'returnArray' => $returnArray,
        ]);
    }

    public function addRemoveProductAction() {
        $succes = true;
        if ($this->getRequest()->isPost()) {
            $productListDetailId = $this->params()->fromPost('productListDetailId');
            $modus = $this->params()->fromPost('modus');
            $newQuantityProductListDetail = '';
            $productQuantityId = '';
            if (!empty($productListDetailId) && !empty($modus)) {
                $productListDetail = $this->em
                        ->getRepository('Grocery\Entity\ProductListDetail')
                        ->findOneBy(array('id' => $productListDetailId));

                $quantityProductListDetail = $productListDetail->getQuantity();

                if ($modus == 'add') {
                    $newQuantityProductListDetail = $quantityProductListDetail + 1;
                    $productListDetail->setQuantity($newQuantityProductListDetail);
                    $this->em->persist($productListDetail);
                    $this->em->flush();
                    $productQuantityId = $productListDetail->getProduct()->getProductId();
                    $succes = true;
                } else {
                    if ($quantityProductListDetail > 0) {
                        $newQuantityProductListDetail = $quantityProductListDetail - 1;
                        $productListDetail->setQuantity($newQuantityProductListDetail);
                        $this->em->persist($productListDetail);
                        $this->em->flush();
                        $productQuantityId = $productListDetail->getProduct()->getProductId();
                        $succes = true;
                    } else {
                        $succes = false;
                    }
                }

                //Create grocery log
                $groceryLog = new GroceryLog();
                $groceryLog->setCrudType('U');
                $groceryLog->setDateTime(new \DateTime());
                $groceryLog->setUser($this->identity());
                $groceryLog->setProductListDetail($productListDetail);
                $this->em->persist($groceryLog);
                $this->em->flush();
            } else {
                $succes = false;
            }
        }

        return new JsonModel([
            'succes' => $succes,
            'quantity' => $newQuantityProductListDetail,
            'productQuantityId' => $productQuantityId,
        ]);
    }

    public function removeProductAction() {
        $succes = true;
        if ($this->getRequest()->isPost()) {
            $productListDetailId = $this->params()->fromPost('productListDetailId');
            if (!empty($productListDetailId)) {
                $productListDetail = $this->em
                        ->getRepository('Grocery\Entity\ProductListDetail')
                        ->findOneBy(array('id' => $productListDetailId));

                if (!empty($productListDetail)) {
                    $productListDetail->setDeleted(1);
                    $this->em->persist($productListDetail);

                    //Create grocery log
                    $groceryLog = new GroceryLog();
                    $groceryLog->setCrudType('D');
                    $groceryLog->setDateTime(new \DateTime());
                    $groceryLog->setUser($this->identity());
                    $groceryLog->setProductListDetail($productListDetail);
                    $this->em->persist($groceryLog);
                    $this->em->flush();

                    $this->em->flush();
                    $succes = true;
                } else {
                    $succes = false;
                }
            } else {
                $succes = false;
            }
        }

        return new JsonModel([
            'succes' => $succes,
        ]);
    }

}
