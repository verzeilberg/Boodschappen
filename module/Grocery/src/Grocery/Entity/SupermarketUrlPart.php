<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="supermarket_url_parts")
 */
class SupermarketUrlPart {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Annotation\Options({
     * "label": "Part number",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Part number"})
     */
    protected $partNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Annotation\Options({
     * "label": "Part description",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Part description"})
     */
    protected $partDescription;
    
       /**
     * Many Url parts have One supermarket.
     * @ORM\ManyToOne(targetEntity="Supermarket", inversedBy="urlParts")
     * @ORM\JoinColumn(name="website_id", referencedColumnName="id")
     */
    private $product;

    function getId() {
        return $this->id;
    }

    function getPartNumber() {
        return $this->partNumber;
    }

    function getPartDescription() {
        return $this->partDescription;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setPartNumber($partNumber) {
        $this->partNumber = $partNumber;
    }

    function setPartDescription($partDescription) {
        $this->partDescription = $partDescription;
    }

    function getProduct() {
        return $this->product;
    }

    function setProduct($product) {
        $this->product = $product;
    }



}
