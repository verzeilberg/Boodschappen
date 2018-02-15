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

class GroceryProductAjaxController extends AbstractActionController {

    /**

     * @var \Blog\Service\PostServiceInterface

     */
    protected $em;
    protected $ps;
    protected $gms;

    public function __construct(EntityManager $doctrineService, $ps, $gms) {
        $this->em = $doctrineService;
        $this->ps = $ps;
        $this->gms = $gms;
    }

    public function deleteImageAction() {

        $succes = true;

        $productId = (int) $this->params()->fromPost('productId', 0);

        $productImageId = (int) $this->params()->fromPost('productImageId', 0);

        if (empty($productId) || empty($productImageId)) {

            $succes = false;
        }



        $product = $this->em
                ->getRepository('Grocery\Entity\Product')
                ->findOneBy(array('productId' => $productId));

        $productImage = $this->em
                ->getRepository('Grocery\Entity\ProductImage')
                ->findOneBy(array('productImageId' => $productImageId));

        if (!$productImage || !$product) {

            $succes = false;
        } else {



            if (count($productImage->getProductImageTypes()) > 0) {

                foreach ($productImage->getProductImageTypes() AS $productImageType) {

                    $this->deleteImageFile($productImageType);

                    $productImage->removeProductImageType($productImageType);

                    $this->em->remove($productImageType);

                    $this->em->persist($productImage);

                    $this->em->flush();
                }
            }



            $product->removeProductImage($productImage);

            $this->em->remove($productImage);

            $this->em->persist($product);

            $this->em->flush();
        }



        return new JsonModel([

            'succes' => $succes,
        ]);
    }

    public function getURLInformationAction() {

        $succes = true;

        $productDetails = array();

        $externalUrl = $this->params()->fromPost('externalUrl', 0);



        if (!empty($externalUrl)) {
            $productArray = $this->ps->returnAHJsonArrayBAsedOnLink($externalUrl);

            $productDetail = $productArray["_embedded"]["lanes"][4]["_embedded"]["items"][0]["_embedded"]["product"];

            //Set product details in array
            $productDetails['name'] = $this->ps->getCleanProductName($productDetail["description"]);
            $productDetails['price'] = $productDetail["priceLabel"]["now"];

            //Set product group ID
            $productCategory = explode('/', $productDetail["categoryName"]);

            $qb = $this->em->getRepository('Grocery\Entity\ProductGroup')->createQueryBuilder('pg');

            $orX = $qb->expr()->orX();

            $orX->add($qb->expr()->like('pg.name', $qb->expr()->literal("%$productCategory[0]%")));

            $qb->where($orX);

            $productDetails['category'] = '';
            try {
                $productGroup = $qb->getQuery()->getSingleResult();
                $productDetails['category'] = $productGroup->getId();
            } catch (\Doctrine\ORM\NoResultException $e) {
                
            }






            $productDescription = $productDetail["details"]["summary"];

            $productDescription = str_replace('[list]', '<ul>', $productDescription);

            $productDescription = str_replace('[/list]', '</ul>', $productDescription);

            $productDescription = str_replace('[*]', '</li><li>', $productDescription);

            $productDescription = str_replace('[b]', '<b>', $productDescription);
            $productDescription = str_replace('[/b]', '</b>', $productDescription);

            $productDetails['description'] = $productDescription;

            $productDetails['imageTitle'] = str_replace(chr(194), "", $productDetail["images"][2]["title"]);

            $productDetails['imageURL'] = $productDetail["images"][2]["link"]["href"];

            $succes = true;
        } else {

            $error_message = 'Geen url';

            $succes = false;
        }



        return new JsonModel([

            'productDetails' => $productDetails,
            'succes' => $succes,
        ]);
    }

    public function sortImagesAction() {

        $succes = true;

        $productImages = $this->params()->fromPost('productImages', 0);

        if (count($productImages) == 0) {

            $succes = false;
        }



        foreach ($productImages AS $index => $productImageId) {

            $productImage = $this->em
                    ->getRepository('Grocery\Entity\ProductImage')
                    ->findOneBy(array('productImageId' => $productImageId));

            $productImage->setSortOrder((int) $index);

            $this->em->persist($productImage);

            $this->em->flush();

            $succes = true;
        }



        return new JsonModel([

            'succes' => $succes,
        ]);
    }

    public function deleteImageFile($productImage) {

        if (is_object($productImage)) {

            $link = 'public/' . $productImage->getFolder() . $productImage->getFileName();

            unlink($link);
        }
    }

    public function addSuggestionAction() {
        $succes = true;
        $error_message = '';
        $suggestionUrl = trim($this->params()->fromPost('suggestionUrl', 0));
        $urlCheck = $this->checkURL($suggestionUrl);
        if ($urlCheck) {

            $suggestion = $this->em
                    ->getRepository('Grocery\Entity\ProductSuggestion')
                    ->findOneBy(array('link' => $suggestionUrl));

            if (empty($suggestion)) {

                $productArray = $this->ps->returnAHJsonArrayBAsedOnLink($suggestionUrl);
                $productDetail = $productArray["_embedded"]["lanes"][4]["_embedded"]["items"][0]["_embedded"]["product"];

                //Set product details in array
                $productName = $this->ps->getCleanProductName($productDetail["description"]);

                $product = $this->em
                        ->getRepository('Grocery\Entity\Product')
                        ->findOneBy(array('name' => $productName));

                if (empty($product)) {
                    $suggestion = new \Grocery\Entity\ProductSuggestion();
                    $suggestion->setLink($suggestionUrl);
                    $suggestion->setProductName($productName);
                    $this->em->persist($suggestion);
                    $this->em->flush();

                    $this->gms->sendSuggestionMail();
                } else {
                    $succes = false;
                    $error_message = 'Product bestaat al doe er geen suggestie meer voor ;)';
                }
            } else {
                $succes = false;
                $error_message = 'Er is al een suggestie gedaan voor dit product!';
            }
        } else {
            $succes = false;
            $error_message = 'Geen of verkeerde url ingevoerd!';
        }
        return new JsonModel([

            'error_message' => $error_message,
            'succes' => $succes,
        ]);
    }

    private function checkURL($url) {
        if (!empty($url)) {
            $rest = substr ($url, 0,  17);
            
            if($rest = 'https://www.ah.nl')
            {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
