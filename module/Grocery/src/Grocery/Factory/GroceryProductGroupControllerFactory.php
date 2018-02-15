<?php
namespace Grocery\Factory;

use Grocery\Controller\GroceryProductGroupController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

class GroceryProductGroupControllerFactory implements FactoryInterface {

     public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ObjectManager $em */
         $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        return new GroceryProductGroupController($em);
    }

}
