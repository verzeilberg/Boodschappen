<?php

namespace Grocery\Factory;

use Grocery\Controller\GroceryCronJobController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use Grocery\Service\groceryMailService;

class GroceryCronJobControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        /** @var ObjectManager $em */
        $parentLocator = $serviceLocator->getServiceLocator();
        
        $request = $parentLocator->get('Request');
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $config = $parentLocator->get('Config');
        $serverUrl = $parentLocator->get('ViewHelperManager')->get('ServerUrl');
        $gms = new groceryMailService($em, $config, $serverUrl, $request);
        return new GroceryCronJobController($em, $gms);
    }

}
