<?php

namespace Grocery\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class productImageService implements productImageServiceInterface {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function deleteProductImages($productImages, $product) {
        foreach ($productImages AS $productImage) {
            if (count($productImage->getProductImageTypes()) > 0) {
                foreach ($productImage->getProductImageTypes() AS $productImageType) {
                    $this->deleteImageFile($productImageType);
                    $productImage->removeProductImageType($productImageType);
                    $this->em->remove($productImageType);
                    $this->em->flush();
                }
                $product->removeProductImage($productImage);
                $this->em->remove($productImage);
                $this->em->flush();
            }
        }
    }

    public function deleteImageFile($productImageType) {
        if (is_object($productImageType)) {
            $link = 'public/' . $productImageType->getFolder() . $productImageType->getFileName();
            @unlink($link);
        }
    }

}
