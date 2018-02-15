<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Productlist
 *
 * @ORM\Entity
 * @ORM\Table(name="productlists", indexes={@ORM\Index(name="search_idx", columns={"weeknumber"})})
 */
class ProductList {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=4, nullable=false)
     * * @Annotation\Options({
     * "label": "Year",
     * })
     */
    protected $year;

    /**
     * @ORM\Column(type="integer", length=2, nullable=false)
     * * @Annotation\Options({
     * "label": "Weeknumber",
     * })
     */
    protected $weeknumber;
    
        /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     * * @Annotation\Options({
     * "label": "Weekelijks budget",
     * })
     */
    protected $weeklyBudget;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * * @Annotation\Options({
     * "label": "Date created",
     * })
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * * @Annotation\Options({
     * "label": "Date ordered",
     * })
     */
    protected $dateOrdered;

    /**
     * One ProductList has Many ProductListDetails.
     * @ORM\OneToMany(targetEntity="ProductListDetail", mappedBy="productList")
     */
    private $productListDetails;

    public function __construct() {
        $this->productListDetails = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getYear() {
        return $this->year;
    }

    function getWeeknumber() {
        return $this->weeknumber;
    }

    function getDateCreated() {
        return $this->dateCreated;
    }

    function getDateOrdered() {
        return $this->dateOrdered;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setYear($year) {
        $this->year = $year;
    }

    function setWeeknumber($weeknumber) {
        $this->weeknumber = $weeknumber;
    }

    function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;
    }

    function setDateOrdered($dateOrdered) {
        $this->dateOrdered = $dateOrdered;
    }

    function getProductListDetails() {
        return $this->productListDetails;
    }
    
    
    function getUndeletedProductListDetails(){
        $productListDetails = array();
        foreach($this->productListDetails AS $productListDetail){
            if($productListDetail->getDeleted() == 0){
                $productListDetails[] = $productListDetail;
            }
        }
        
        return $productListDetails;
    }
    

    function setProductListDetails($productListDetails) {
        $this->productListDetails = $productListDetails;
    }

    function getTotalCost() {
        $totalCost = 0;
        if ($this->getUndeletedProductListDetails() != NULL) {
            foreach ($this->getUndeletedProductListDetails() AS $productListDetail) {
                $productPrice = $productListDetail->getProduct()->getPrice();
                if ($productPrice != 0 OR ! empty($productPrice)) {
                    $totalCost = $totalCost + ($productListDetail->getQuantity() * $productPrice);
                }
            }
        }
        return $totalCost;
    }

}
