<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="productimages")
 */
class ProductImage {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $productImageId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Required(false)
     * @Annotation\Options({
     * "label": "Afbeelding naam",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Afbeelding naam"})
     */
    protected $nameImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Required(false)
     * @Annotation\Options({
     * "label": "Alt tekst",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Alt tekst"})
     */
    protected $alt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Required(false)
     * @Annotation\Options({
     * "label": "Omschrijving",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Omschrijving"})
     */
    protected $descriptionImage;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true);
     * @Annotation\Required(false)
     */
    protected $sortOrder = 0;

    /**
     * Many productImages have Many productImageTypes.
     * @ORM\ManyToMany(targetEntity="ProductImageType")
     * @ORM\JoinTable(name="productimage_productimagetypes",
     *      joinColumns={@ORM\JoinColumn(name="imageId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="imageTypeId", referencedColumnName="id", unique=true)}
     *      )
     */
    private $productImageTypes;

    public function __construct() {
        $this->productImageTypes = new ArrayCollection();
    }

    function getProductImageTypes($imageType = NULL) {
        if ($imageType === NULL) {
            return $this->productImageTypes;
        } else {
            $productImageTypes = array();
            foreach ($this->productImageTypes AS $productImageType) {
                if ($productImageType->getImageTypeName() == $imageType) {
                    $productImageTypes[] = $productImageType;
                }
            }
            return $productImageTypes;
        }
    }

    function setProductImageTypes($productImages) {
        $this->productImageTypes = $productImages;
    }

    public function addProductImageType(ProductImageType $productImageType) {
        if (!$this->productImageTypes->contains($productImageType)) {
            $this->productImageTypes->add($productImageType);
        }
        return $this;
    }

    public function removeProductImageType(ProductImageType $productImageType) {
        if ($this->productImageTypes->contains($productImageType)) {
            $this->productImageTypes->removeElement($productImageType);
        }
        return $this;
    }

    function getAlt() {
        return $this->alt;
    }

    function setAlt($alt) {
        $this->alt = $alt;
    }

    function getProductImageId() {
        return $this->productImageId;
    }

    function setProductImageId($productImageId) {
        $this->productImageId = $productImageId;
    }
    function getSortOrder() {
        return $this->sortOrder;
    }

    function setSortOrder($sortOrder) {
        $this->sortOrder = $sortOrder;
    }

    function getNameImage() {
        return $this->nameImage;
    }

    function getDescriptionImage() {
        return $this->descriptionImage;
    }

    function setNameImage($nameImage) {
        $this->nameImage = $nameImage;
    }

    function setDescriptionImage($descriptionImage) {
        $this->descriptionImage = $descriptionImage;
    }


}
