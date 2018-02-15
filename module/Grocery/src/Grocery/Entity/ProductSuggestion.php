<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="productsuggestions")
 */
class ProductSuggestion {

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
     * @Annotation\Attributes({"class":"form-control", "placeholder":"product naam"})
     */
    protected $productName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Annotation\Options({
     * "label": "Link naar product",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Link"})
     */
    protected $link;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     * * @Annotation\Options({
     * "label": "Product goedgekeurd",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Link"})
     */
    protected $approve = 0;

    /**
     * One ProductListDetail has Many GroceryLogs.
     * @ORM\OneToMany(targetEntity="GroceryLog", mappedBy="productSuggestion")
     */
    private $groceryLogs;

    public function __construct() {
        $this->groceryLogs = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getLink() {
        return $this->link;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setLink($link) {
        $this->link = $link;
    }

    function getProductName() {
        return $this->productName;
    }

    function setProductName($productName) {
        $this->productName = $productName;
    }

    function getGroceryLogs() {
        return $this->groceryLogs;
    }

    function setGroceryLogs($groceryLogs) {
        $this->groceryLogs = $groceryLogs;
    }

    function getApprove() {
        return $this->approve;
    }

    function setApprove($approve) {
        $this->approve = $approve;
    }

}
