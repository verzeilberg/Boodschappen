<?php

namespace Grocery\Factory;

use Grocery\Controller\GroceryProductListController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

class GroceryProductListControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        /** @var ObjectManager $em */
        $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $vhm = $parentLocator->get('viewhelpermanager');
        return new GroceryProductListController($em, $vhm);
    }

}
