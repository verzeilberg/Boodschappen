<?php
namespace Grocery\Factory;

use Grocery\Controller\GroceryProductAjaxController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use Grocery\Service\productService;
use Grocery\Service\groceryMailService;
use UploadImages\Service\cropImageService;

class GroceryProductAjaxControllerFactory implements FactoryInterface {

     public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ObjectManager $em */
         $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $request = $parentLocator->get('Request');
        $config = $parentLocator->get('Config');
        $serverUrl = $parentLocator->get('ViewHelperManager')->get('ServerUrl');
        $ps = new productService($em);
        $gms = new groceryMailService($em, $config, $serverUrl, $request);
        return new GroceryProductAjaxController($em, $ps, $gms);
    }

}
