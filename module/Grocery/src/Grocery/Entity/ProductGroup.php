<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="productgroups")
 */
class ProductGroup {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Annotation\Options({
     * "label": "Product naam",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $name;

    /**
     * @ORM\Column(type="text", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "Omschrijving",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Omschrijving"})
     */
    protected $description;

    /**
     * Many Groups have Many Products.
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="productGroups")
     */
    private $products;

    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addProducts(Collection $products) {
        foreach ($products as $product) {
            $this->products->add($product);
        }
    }

    public function removeProducts(Collection $products) {
        foreach ($products as $product) {
            $this->products->removeElement($product);
        }
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getDescription() {
        return $this->description;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDescription($description) {
        $this->description = $description;
    }
    
    function getProducts() {
        return $this->products;
    }

    function setProducts($products) {
        $this->products = $products;
    }



}
