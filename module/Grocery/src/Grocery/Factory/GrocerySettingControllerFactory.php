<?php
namespace Grocery\Factory;

use Grocery\Controller\GrocerySettingController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

class GrocerySettingControllerFactory implements FactoryInterface {

     public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ObjectManager $em */
         $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $vhm = $parentLocator->get('viewhelpermanager');
        return new GrocerySettingController($em, $vhm);
    }

}
