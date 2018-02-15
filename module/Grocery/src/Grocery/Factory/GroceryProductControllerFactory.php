<?php
namespace Grocery\Factory;

use Grocery\Controller\GroceryProductController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use UploadImages\Service\cropImageService;
use Grocery\Service\productService;
use Grocery\Service\productListService;
use Grocery\Service\productImageService;

class GroceryProductControllerFactory implements FactoryInterface {

     public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ObjectManager $em */
         $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $config = $parentLocator->get('config');
        $cis = new cropImageService($em, $config);
        $ps = new productService($em);
        $pls = new productListService($em);
        $pis = new productImageService($em);
        $vhm = $parentLocator->get('viewhelpermanager');
        return new GroceryProductController($em, $cis, $vhm, $ps, $pls, $pis);
    }

}
