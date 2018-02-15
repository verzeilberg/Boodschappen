<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="grocerylog")
 */
class GroceryLog {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    protected $crudType;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=false)
     */
    protected $dateTime;

    /**
     * Many Logs have One User.
     * @ORM\ManyToOne(targetEntity="SiteUser\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Many GroceryLog have One ProductListDetail.
     * @ORM\ManyToOne(targetEntity="Grocery\Entity\ProductListDetail", inversedBy="groceryLogs")
     * @ORM\JoinColumn(name="product_list_detail_id", referencedColumnName="id")
     */
    private $productListDetail;

    /**
     * Many GroceryLog have One ProductSuggestion.
     * @ORM\ManyToOne(targetEntity="Grocery\Entity\ProductSuggestion", inversedBy="groceryLogs")
     * @ORM\JoinColumn(name="product_suggestion_id", referencedColumnName="id")
     */
    private $productSuggestion;

    function getId() {
        return $this->id;
    }

    function getCrudType() {
        return $this->crudType;
    }

    function getUser() {
        return $this->user;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCrudType($crudType) {
        $this->crudType = $crudType;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function getDateTime() {
        return $this->dateTime;
    }

    function getProductListDetail() {
        return $this->productListDetail;
    }

    function setDateTime($dateTime) {
        $this->dateTime = $dateTime;
    }

    function setProductListDetail($productListDetail) {
        $this->productListDetail = $productListDetail;
    }

    function getProductSuggestion() {
        return $this->productSuggestion;
    }

    function setProductSuggestion($productSuggestion) {
        $this->productSuggestion = $productSuggestion;
    }

}
