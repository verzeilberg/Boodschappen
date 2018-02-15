<?php
namespace Grocery\Factory;

use Grocery\Controller\GroceryProductFactController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

class GroceryProductFactControllerFactory implements FactoryInterface {

     public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ObjectManager $em */
         $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        return new GroceryProductFactController($em);
    }

}
