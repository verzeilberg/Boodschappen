<?php
namespace Application\Factory;

use Application\Controller\IndexController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use Grocery\Service\productService;

class IndexControllerFactory implements FactoryInterface {

     public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ObjectManager $em */
         $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $ps = new productService($em);
        return new IndexController($em, $ps);
    }

}
