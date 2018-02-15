<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="productlistdetails")
 */
class ProductListDetail {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $productListId;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $productId;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     * * @Annotation\Options({
     * "label": "Hoeveelheid",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Hoeveelheid"})
     */
    protected $quantity = 1;

    /**
     * Many ProductListDetail have One ProductList.
     * @ORM\ManyToOne(targetEntity="ProductList", inversedBy="productListDetails")
     * @ORM\JoinColumn(name="productListId", referencedColumnName="id")
     */
    private $productList;

    /**
     * Many ProductListDetail have One Product.
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productListDetails")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;
    
    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted = 0;

    /**
     * One ProductListDetail has Many GroceryLogs.
     * @ORM\OneToMany(targetEntity="GroceryLog", mappedBy="productListDetail")
     */
    private $groceryLogs;

    public function __construct() {
        $this->groceryLogs = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    function getProductList() {
        return $this->productList;
    }

    function setProductList($productList) {
        $this->productList = $productList;
    }

    function getProduct() {
        return $this->product;
    }

    function setProduct($product) {
        $this->product = $product;
    }

    function getProductListId() {
        return $this->productListId;
    }

    function getProductId() {
        return $this->productId;
    }

    function getGroceryLogs() {
        return $this->groceryLogs;
    }

    function setProductListId($productListId) {
        $this->productListId = $productListId;
    }

    function setProductId($productId) {
        $this->productId = $productId;
    }

    function setGroceryLogs($groceryLogs) {
        $this->groceryLogs = $groceryLogs;
    }

    function getDeleted() {
        return $this->deleted;
    }

    function setDeleted($deleted) {
        $this->deleted = $deleted;
    }



}
