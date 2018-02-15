<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/*
 * Entities
 */

/**
 * ProductFacts
 *
 * @ORM\Entity
 * @ORM\Table(name="productfacts")
 */
class ProductFact {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $productFactId;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "Product weetje titel",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Product weetje titel"})
     */
    protected $productFactTitle;

    /**
     * @ORM\Column(type="text", nullable=false)
     * * @Annotation\Options({
     * "label": "Product weetje omschrijving",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Omschrijving"})
     */
    protected $productFactDescription;

    /**
     * Many ProductFacts have One Product.
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productFacts")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id", onDelete="CASCADE")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "empty_option": "---",
     * "target_class":"Grocery\Entity\Product",
     * "property": "name",
     * "label": "Product",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"},
     * })
     * @Annotation\Attributes({"class": "form-control"})
     */
    private $product;

    function getProductFactId() {
        return $this->productFactId;
    }

    function getProductFactTitle() {
        return $this->productFactTitle;
    }

    function getProductFactDescription() {
        return $this->productFactDescription;
    }

    function getProduct() {
        return $this->product;
    }

    function setProductFactId($productFactId) {
        $this->productFactId = $productFactId;
    }

    function setProductFactTitle($productFactTitle) {
        $this->productFactTitle = $productFactTitle;
    }

    function setProductFactDescription($productFactDescription) {
        $this->productFactDescription = $productFactDescription;
    }

    function setProduct($product) {
        $this->product = $product;
    }

}
