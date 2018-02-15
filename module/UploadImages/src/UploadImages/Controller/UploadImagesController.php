<?php

namespace UploadImages\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use UploadImages\Service\cropImageServiceInterface;
use Zend\Session\Container;

/*
 * Entities
 */
use UploadImages\Entity\Image;

class UploadImagesController extends AbstractActionController {

    protected $cropImageService;
    protected $vhm;

    public function __construct(cropImageServiceInterface $cropImageService, $vhm) {
        $this->cropImageService = $cropImageService;
        $this->vhm = $vhm;
    }

    public function cropAction() {
        $this->vhm->get('headScript')->appendFile('/js/jquery.Jcrop.min.js');
        $this->vhm->get('headLink')->appendStylesheet('/css/jCrop/jquery.Jcrop.min');
        $container = new Container('cropImages');
        $aCropDetails = $container->offsetGet('cropimages');
        $aReturnURL = $container->offsetGet('returnUrl');
        $returnURLRoute = $aReturnURL['route'];
        $returnURLAction = $aReturnURL['action'];

        $iXcrops = count($aCropDetails);
        
        if(empty($aCropDetails)) {
            return $this->redirect()->toRoute($returnURLRoute, array('action' => $returnURLAction));
        }
        
        //Get the first item in the array
        $oCropDetails = $aCropDetails[0];

        //Split session array into varibles
        $sImageToBeCropped = $oCropDetails['originalLink']; //link of the image that must be cropped
        $sCropReference = $oCropDetails['imageType']; //Reference of the crop
        $sDestionationFolderCroppedImage = $oCropDetails['destinationFolder']; //Folder where the image has to be saved
        $iImgW = (int)$oCropDetails['ImgW']; //Image width
        $iImgH = (int)$oCropDetails['ImgH']; //Image height
        // Get the widht and height of the orignal image        
        $aFileProps = getimagesize('public/' . $sImageToBeCropped);
        
        $iWidth = (int)$aFileProps[0];
        $iHeight = (int)$aFileProps[1];

        if ($iImgW > $iWidth || $iImgH > $iHeight) {
            array_shift($aCropDetails);
            $container->cropimages = $aCropDetails;
            $this->flashMessenger()->addErrorMessage('Crop size is to large for orginal image');
            if (empty($aCropDetails)) {
                $container->getManager()->getStorage()->clear('cropImages');
                return $this->redirect()->toRoute($returnURLRoute, array('action' => $returnURLAction));
            } else {
                return $this->redirect()->toRoute('images', array('action' => 'crop'));
            }
        }


        //if user crops image
        if ($this->getRequest()->isPost()) {
            $x = $this->getRequest()->getPost('x');
            $y = $this->getRequest()->getPost('y');
            $w = $this->getRequest()->getPost('w');
            $h = $this->getRequest()->getPost('h');
            
            //Crop image
            $result = $this->cropImageService->CropImage('public/' . $sImageToBeCropped, $sDestionationFolderCroppedImage, $x, $y, $w, $h, $iImgW, $iImgH);
            
            
            # Delete the first item in the array
            array_shift($aCropDetails);
            $container->cropimages = $aCropDetails;
            
            # Check if the array is empty
            if (empty($aCropDetails)) {
                $container->getManager()->getStorage()->clear('cropImages');
                # set status update
                return $this->redirect()->toRoute($returnURLRoute, array('action' => $returnURLAction));
            } else {
                return $this->redirect()->toRoute('images', array('action' => 'crop'));
            }
        }

        return new ViewModel(
                array(
            'sCropReference' => $sCropReference,
            'sImageToBeCropped' => $sImageToBeCropped,
            'sDestionationFolderCroppedImage' => $sDestionationFolderCroppedImage,
            'iXcrops' => $iXcrops,
                    'iImgW' => $iImgW, 
                    'iImgH' => $iImgH
                )
        );
    }

}
