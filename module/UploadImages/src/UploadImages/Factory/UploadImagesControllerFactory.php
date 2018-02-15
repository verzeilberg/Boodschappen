<?php
namespace UploadImages\Factory;

use UploadImages\Controller\UploadImagesController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use UploadImages\Service\cropImageService;

class UploadImagesControllerFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $parentLocator = $serviceLocator->getServiceLocator();
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $config = $parentLocator->get('config');
        $cis = new cropImageService($em, $config);
        $em = $parentLocator->get('doctrine.entitymanager.orm_default');
        $vhm = $parentLocator->get('viewhelpermanager');
        return new UploadImagesController($cis, $vhm, $em);
    }

}
