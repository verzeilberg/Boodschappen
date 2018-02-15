<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="supermarkets")
 */
class Supermarket {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Annotation\Options({
     * "label": "Supermarkt naam",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Annotation\Options({
     * "label": "Supermarkt website",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"url"})
     */
    protected $website;
    
    /**
     * One supermarket has Many url parts.
     * @ORM\OneToMany(targetEntity="SupermarketUrlPart", mappedBy="supermarket")
     */
    private $urlParts;
    // ...

    public function __construct() {
        $this->features = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getWebsite() {
        return $this->website;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setWebsite($website) {
        $this->website = $website;
    }

    function getUrlParts() {
        return $this->urlParts;
    }

    function setUrlParts($urlParts) {
        $this->urlParts = $urlParts;
    }


}
