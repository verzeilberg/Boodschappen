<?php

namespace Grocery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use Zend\EventManager\EventInterface;
use Zend\Session\Container;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

/*
 * Entities
 */
use Grocery\Entity\Product;
use \Grocery\Entity\ProductImage;
use \Grocery\Entity\ProductImageType;

class GroceryProductController extends AbstractActionController {

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
        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() != 'admin') {
            $this->redirect()->toRoute('home');
        }
        $this->layout('layout/admin');
        $this->vhm->get('headScript')->appendFile('/js/product.js');
        $container = new Container('cropImages');
        $container->getManager()->getStorage()->clear('cropImages');
        $productGroupsForSelect = $this->em->getRepository('Grocery\Entity\ProductGroup')->findAll();
        $productGroups = array();
        $searchTerm = '';
        $qb = $this->em->getRepository('Grocery\Entity\Product')->createQueryBuilder('p');
        if ($this->getRequest()->isPost()) {
            $productGroups = $this->params()->fromPost('productGroups');
            $searchTerm = $this->params()->fromPost('searchString');
            if (!empty($searchTerm)) {
                $qb->leftJoin('p.productGroups', 'pg');
                $orX = $qb->expr()->orX();
                $orX->add($qb->expr()->like('p.name', $qb->expr()->literal("%$searchTerm%")));
                $orX->add($qb->expr()->like('p.productNumber', $qb->expr()->literal("%$searchTerm%")));
                $qb->where($orX);
                if (count($productGroups) > 0) {
                    $qb->andWhere($qb->expr()->in('pg.id', ':productGroepen'));
                    $qb->setParameter('productGroepen', $productGroups);
                }
            }
        }

        $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($qb)));
        $paginator->setCurrentPageNumber($this->params()
                        ->fromQuery('page', 1))
                ->setItemCountPerPage(10);

        return new ViewModel(
                array(
            'products' => $paginator,
            'productGroupsForSelect' => $productGroupsForSelect,
            'searchTerm' => $searchTerm,
            'productGroups' => $productGroups
                )
        );
    }

    public function addAction() {
        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() != 'admin') {
            $this->redirect()->toRoute('home');
        }
        $this->layout('layout/admin');
        $container = new Container('cropImages');
        $container->getManager()->getStorage()->clear('cropImages');
        $this->vhm->get('headScript')->appendFile('/js/product.js');
        $this->vhm->get('headScript')->appendFile('https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=1klk7ssywjyaz8t4ifhuijkplfvth0pw72yx41m5d8ezbl94');
        //$this->vhm->get('headLink')->appendStylesheet('/js/bootstrap3-wysihtml5.css');

        $product = new Product();
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($product);
        $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\Product'));
        $form->bind($product);

        $productImage = new ProductImage();
        $formProductImage = $builder->createForm($productImage);
        $formProductImage->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\ProductImage'));
        $formProductImage->bind($productImage);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            $formProductImage->setData($this->getRequest()->getPost());
            if ($form->isValid() && $formProductImage->isValid()) {
                //Get file from form
                $aImageFile = '';
                $externalURL = $this->getRequest()->getPost('externalURLfield');
                if (!empty($externalURL)) {
                    $temp_name = tempnam(sys_get_temp_dir(), 'php');
                    copy($externalURL, $temp_name);
                    $imageDetails = getimagesize($temp_name);
                    $imageSize = filesize($temp_name);
                    $pos = strrpos($externalURL, '/');
                    $imageName = $pos === false ? $externalURL : substr($externalURL, $pos + 1);
                    $aImageFile = array("name" => $imageName, "type" => $imageDetails["mime"], "tmp_name" => $temp_name, "error" => 0, "size" => $imageSize);
                } else {
                    $aImageFile = $this->getRequest()->getFiles('productImage');
                }

                //Upload image file
                if ($aImageFile['error'] === 0) {
                    //Upload image file
                    $cropImageService = $this->cis;
                    $result = $cropImageService->uploadImage($aImageFile);
                    if (is_array($result)) {
                        $fileName = $result['imageFileName'];
                        $folderOriginal = $result['imageFolderName'];
                        $imageTypeName = 'original';
                        $productImageType = new ProductImageType();
                        $productImageType->setFileName($fileName);
                        $productImageType->setFolder($folderOriginal);
                        $productImageType->setImageTypeName($imageTypeName);

                        $productImage->addProductImageType($productImageType);

                        $this->em->persist($productImageType);
                        $this->em->flush();

                        $this->flashMessenger()->addSuccessMessage('File, ' . $fileName . ' added to product');
                    } else {
                        $this->flashMessenger()->addErrorMessage($result);
                    }
                    if (is_array($result)) {
                        //Make thumb file 150x150
                        $result2 = $cropImageService->resizeAndCropImage('public/' . $folderOriginal . $fileName, 'public/img/userFiles/product/thumb/', 150, 150);
                        if (is_array($result2)) {
                            $fileName = $result['imageFileName'];
                            $folder = 'img/userFiles/product/thumb/';
                            $imageTypeName = '150x150';
                            $ProductImageType = new ProductImageType();
                            $ProductImageType->setFileName($fileName);
                            $ProductImageType->setFolder($folder);
                            $ProductImageType->setImageTypeName($imageTypeName);

                            $productImage->addProductImageType($ProductImageType);

                            $this->em->persist($ProductImageType);
                            $this->em->flush();

                            $this->flashMessenger()->addSuccessMessage('File, ' . $fileName . ' added to product');
                        } else {
                            $this->flashMessenger()->addErrorMessage($result2);
                        }
                    }

                    //Crop image adding
                    if (is_array($result)) {

                        $cropImages = array();
                        $cropimage = array();

                        $cropimage['imageType'] = '25x25';
                        $cropimage['originalLink'] = $folderOriginal . $fileName;
                        $cropimage['destinationFolder'] = 'public/img/userFiles/product/25x25/';
                        $cropimage['ImgW'] = 25;
                        $cropimage['ImgH'] = 25;

                        $cropImages[] = $cropimage;

                        $ProductImageType = new ProductImageType();
                        $ProductImageType->setFileName($fileName);
                        $ProductImageType->setFolder('img/userFiles/product/25x25/');
                        $ProductImageType->setImageTypeName($cropimage['imageType']);

                        $productImage->addProductImageType($ProductImageType);

                        $this->em->persist($ProductImageType);
                        $this->em->flush();

                        $cropimage['imageType'] = '75x75';
                        $cropimage['originalLink'] = $folderOriginal . $fileName;
                        $cropimage['destinationFolder'] = 'public/img/userFiles/product/75x75/';
                        $cropimage['ImgW'] = 75;
                        $cropimage['ImgH'] = 75;

                        $cropImages[] = $cropimage;

                        $ProductImageType = new ProductImageType();
                        $ProductImageType->setFileName($fileName);
                        $ProductImageType->setFolder('img/userFiles/product/75x75/');
                        $ProductImageType->setImageTypeName($cropimage['imageType']);

                        $productImage->addProductImageType($ProductImageType);

                        $this->em->persist($ProductImageType);
                        $this->em->flush();

                        $returnURL = array();
                        $returnURL['route'] = 'groceryProduct';
                        $returnURL['action'] = 'index';

                        $container = new Container('cropImages');
                        $container->cropimages = $cropImages;
                        $container->returnUrl = $returnURL;
                    }
                    //Crop image adding

                    if (is_array($result) && is_array($result2)) {
                        //Save product image
                        $this->em->persist($productImage);
                        $this->em->flush();
                        $product->addProductImage($productImage);
                    }
                }

                //Set index
                $productName = $product->getName();
                $productNameClean = $this->ps->getCleanProductName($productName);
                $product->setIndexString($this->ps->getFirstCharacterOfString($productNameClean));
                //Save product
                $product->setName($productNameClean);
                $this->em->persist($product);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Product opgeslagen');

                if ($aImageFile['error'] === 0 && (is_array($result) && is_array($result2))) {
                    return $this->redirect()->toRoute('images', array('action' => 'crop'));
                } else {
                    return $this->redirect()->toRoute('groceryProduct');
                }
            } else {
                var_dump($form->getMessages());
                var_dump($formProductImage->getMessages());
            }
        }

        return new ViewModel(
                array(
            'form' => $form,
            'formProductImage' => $formProductImage,
                )
        );
    }

    public function changeAction() {
        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() != 'admin') {
            $this->redirect()->toRoute('home');
        }
        $this->layout('layout/admin');
        //$this->vhm->get('headLink')->appendStylesheet('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        $this->vhm->get('headScript')->appendFile('/js/product.js');

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
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($product);
        $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\Product'));
        $form->bind($product);

        $productImage = new ProductImage();
        $formProductImage = $builder->createForm($productImage);
        $formProductImage->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\ProductImage'));
        $formProductImage->bind($productImage);

        if ($this->getRequest()->isPost()) {

            $form->setData($this->getRequest()->getPost());
            $formProductImage->setData($this->getRequest()->getPost());
            if ($form->isValid() && $formProductImage->isValid()) {
                //Get file from form
                $aImageFile = '';
                $aImageFile = $this->getRequest()->getFiles('productImage');
                //Upload image file
                if ($aImageFile['error'] === 0) {
                    //Upload image file
                    $cropImageService = $this->cis;
                    $result = $cropImageService->uploadImage($aImageFile);


                    if (is_array($result)) {
                        $fileName = $result['imageFileName'];
                        $folderOriginal = $result['imageFolderName'];
                        $imageTypeName = 'original';
                        $productImageType = new ProductImageType();
                        $productImageType->setFileName($fileName);
                        $productImageType->setFolder($folderOriginal);
                        $productImageType->setImageTypeName($imageTypeName);

                        $productImage->addProductImageType($productImageType);

                        $this->em->persist($productImageType);
                        $this->em->flush();


                        $this->flashMessenger()->addSuccessMessage('File, ' . $fileName . ' added to product');
                    } else {
                        $this->flashMessenger()->addErrorMessage($result);
                    }

                    if (is_array($result)) {
                        //Make thumb file 150x150
                        $result2 = $cropImageService->resizeAndCropImage('public/' . $folderOriginal . $fileName, 'public/img/userFiles/product/thumb/', 150, 150);
                        if (is_array($result2)) {
                            $fileName = $result['imageFileName'];
                            $folder = 'img/userFiles/product/thumb/';
                            $imageTypeName = '150x150';
                            $ProductImageType = new ProductImageType();
                            $ProductImageType->setFileName($fileName);
                            $ProductImageType->setFolder($folder);
                            $ProductImageType->setImageTypeName($imageTypeName);

                            $productImage->addProductImageType($ProductImageType);

                            $this->em->persist($ProductImageType);
                            $this->em->flush();

                            $this->flashMessenger()->addSuccessMessage('File, ' . $fileName . ' added to product');
                        } else {
                            $this->flashMessenger()->addErrorMessage($result2);
                        }
                    }
                    //Crop image adding
                    if (is_array($result)) {

                        $cropImages = array();
                        $cropimage = array();

                        $cropimage['imageType'] = '25x25';
                        $cropimage['originalLink'] = $folderOriginal . $fileName;
                        $cropimage['destinationFolder'] = 'public/img/userFiles/product/25x25/';
                        $cropimage['ImgW'] = 25;
                        $cropimage['ImgH'] = 25;

                        $cropImages[] = $cropimage;

                        $ProductImageType = new ProductImageType();
                        $ProductImageType->setFileName($fileName);
                        $ProductImageType->setFolder('img/userFiles/product/25x25/');
                        $ProductImageType->setImageTypeName($cropimage['imageType']);

                        $productImage->addProductImageType($ProductImageType);

                        $this->em->persist($ProductImageType);
                        $this->em->flush();

                        $cropimage['imageType'] = '75x75';
                        $cropimage['originalLink'] = $folderOriginal . $fileName;
                        $cropimage['destinationFolder'] = 'public/img/userFiles/product/75x75/';
                        $cropimage['ImgW'] = 75;
                        $cropimage['ImgH'] = 75;

                        $cropImages[] = $cropimage;

                        $ProductImageType = new ProductImageType();
                        $ProductImageType->setFileName($fileName);
                        $ProductImageType->setFolder('img/userFiles/product/75x75/');
                        $ProductImageType->setImageTypeName($cropimage['imageType']);

                        $productImage->addProductImageType($ProductImageType);

                        $this->em->persist($ProductImageType);
                        $this->em->flush();

                        $returnURL = array();
                        $returnURL['route'] = 'groceryProduct';
                        $returnURL['action'] = 'index';

                        $container = new Container('cropImages');
                        $container->cropimages = $cropImages;
                        $container->returnUrl = $returnURL;
                    }
                    //Crop image adding
                    if (is_array($result) && is_array($result2)) {
                        //Save product image
                        $this->em->persist($productImage);
                        $this->em->flush();
                        $product->addProductImage($productImage);
                    }
                }


                $this->em->persist($product);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Product gewijzigd');

                if (is_array($result) && is_array($result2)) {
                    return $this->redirect()->toRoute('images', array('action' => 'crop'));
                } else {
                    return $this->redirect()->toRoute('groceryProduct');
                }
            } else {
                var_dump($form->getMessages());
                var_dump($formProductImage->getMessages());
            }
        }
        return new ViewModel(
                array(
            'form' => $form,
            'formProductImage' => $formProductImage,
            'product' => $product
                )
        );
    }

    public function deleteAction() {
        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() != 'admin') {
            $this->redirect()->toRoute('home');
        }
        $this->layout('layout/admin');
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
        $productImages = $product->getProductImages();
        if (count($productImages) > 0) {
            $this->pis->deleteProductImages($productImages, $product);
        }
        $this->em->remove($product);
        $this->em->flush();
        $this->flashMessenger()->addSuccessMessage('Product verwijderen');
        return $this->redirect()->toRoute('groceryProduct');
    }

    public function detailAction() {
        $this->vhm->get('headScript')->appendFile('/js/product.js');
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

        return new ViewModel(
                array(
            'product' => $product
                )
        );
    }

    public function productSuggestionsAction() {
        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() != 'admin') {
            $this->redirect()->toRoute('home');
        }
        $this->layout('layout/admin');
        $this->vhm->get('headScript')->appendFile('/js/product.js');
        $productSuggestions = $this->em->getRepository('Grocery\Entity\ProductSuggestion')->findBy(array('approve' => 0));
        $productSuggestionsDeclined = $this->em->getRepository('Grocery\Entity\ProductSuggestion')->findBy(array('approve' => 2));
        $productSuggestionsAccepted = $this->em->getRepository('Grocery\Entity\ProductSuggestion')->findBy(array('approve' => 1));
        return new ViewModel(
                array(
            'productSuggestions' => $productSuggestions,
            'productSuggestionsDeclined' => $productSuggestionsDeclined,
            'productSuggestionsAccepted' => $productSuggestionsAccepted
                )
        );
    }

    public function addDeclineProductSuggestionAction() {
        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() != 'admin') {
            $this->redirect()->toRoute('home');
        }
        $this->layout('layout/admin');
        //Get variables from route
        $id = (int) $this->params()->fromRoute('id');
        $approve = (int) $this->params()->fromRoute('approve');
        if (empty($id) || empty($approve)) {
            return $this->redirect()->toRoute('groceryProduct', array('action' => 'productSuggestions'));
        }

        if ($approve == 1) {
            $this->vhm->get('headScript')->appendFile('/js/product.js');
            $this->vhm->get('headScript')->appendFile('https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=1klk7ssywjyaz8t4ifhuijkplfvth0pw72yx41m5d8ezbl94');

            $productSuggestion = $this->em->getRepository('Grocery\Entity\ProductSuggestion')->find($id);
            if (empty($productSuggestion)) {
                return $this->redirect()->toRoute('groceryProduct', array('action' => 'productSuggestions'));
            }

            $product = new Product();
            $builder = new AnnotationBuilder($this->em);
            $form = $builder->createForm($product);
            $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\Product'));
            $form->bind($product);

            $externalUrl = $productSuggestion->getLink();
            $productArray = $this->ps->returnAHJsonArrayBAsedOnLink($externalUrl);

            $productDetail = $productArray["_embedded"]["lanes"][4]["_embedded"]["items"][0]["_embedded"]["product"];

            //Set product details in array
            $productName = $this->ps->getCleanProductName($productDetail["description"]);
            $productPrice = $productDetail["priceLabel"]["now"];

            $form->get('name')->setValue($productName);
            $form->get('price')->setValue($productPrice);

            $productDescription = $productDetail["details"]["summary"];
            $productDescription = str_replace('[list]', '<ul>', $productDescription);
            $productDescription = str_replace('[/list]', '</ul>', $productDescription);
            $productDescription = str_replace('[*]', '</li><li>', $productDescription);
            $productDescription = str_replace('[b]', '<b>', $productDescription);
            $productDescription = str_replace('[/b]', '</b>', $productDescription);

            $form->get('description')->setValue($productDescription);


            //Set product group ID
            $productCategory = explode('/', $productDetail["categoryName"]);

            $qb = $this->em->getRepository('Grocery\Entity\ProductGroup')->createQueryBuilder('pg');

            $orX = $qb->expr()->orX();

            $orX->add($qb->expr()->like('pg.name', $qb->expr()->literal("%$productCategory[0]%")));

            $qb->where($orX);

            $productCategory = '';
            try {
                $productGroup = $qb->getQuery()->getSingleResult();

                $form->get('productGroups')->setValue($productGroup);
            } catch (\Doctrine\ORM\NoResultException $e) {
                
            }


            $productImage = new ProductImage();
            $formProductImage = $builder->createForm($productImage);
            $formProductImage->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\ProductImage'));
            $formProductImage->bind($productImage);

            $productImageTitle = str_replace(chr(194), "", $productDetail["images"][2]["title"]);
            $formProductImage->get('nameImage')->setValue($productImageTitle);
            $formProductImage->get('alt')->setValue($productImageTitle);

            $productImageURL = $productDetail["images"][2]["link"]["href"];

            $productSuggestion->setApprove($approve);
            $this->em->persist($productSuggestion);
            $this->em->flush();

            return new ViewModel(
                    array(
                'form' => $form,
                'formProductImage' => $formProductImage,
                'productImageURL' => $productImageURL
                    )
            );
        }


        if ($approve == 2) {
            $productSuggestion = $this->em->getRepository('Grocery\Entity\ProductSuggestion')->find($id);
            if (empty($productSuggestion)) {
                return $this->redirect()->toRoute('groceryProduct', array('action' => 'productSuggestions'));
            }

            $productSuggestion->setApprove($approve);
            $this->em->persist($productSuggestion);
            $this->em->flush();
            $this->flashMessenger()->addSuccessMessage('Product suggestie afgewezen');
            return $this->redirect()->toRoute('groceryProduct', array('action' => 'productSuggestions'));
        }
    }

    public function removeProductSuggestionAction() {
        if ($this->identity() && $this->identity()->getRoles()[0]->getRoleId() != 'admin') {
            $this->redirect()->toRoute('home');
        }
        $this->layout('layout/admin');
        //Get variables from route
        $id = (int) $this->params()->fromRoute('id');

        if (empty($id)) {
            return $this->redirect()->toRoute('groceryProduct', array('action' => 'productSuggestions'));
        }

        $productSuggestion = $this->em->getRepository('Grocery\Entity\ProductSuggestion')->find($id);
        if (empty($productSuggestion)) {
            return $this->redirect()->toRoute('groceryProduct', array('action' => 'productSuggestions'));
        }

        $this->em->remove($productSuggestion);
        $this->em->flush();
        $this->flashMessenger()->addSuccessMessage('Product suggestie verwijderd');
        return $this->redirect()->toRoute('groceryProduct', array('action' => 'productSuggestions'));
    }

}
