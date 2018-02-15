<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="grocerybudget")
 */
class GroceryBudget {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     * * @Annotation\Options({
     * "label": "Bedrag",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Bedrag"})
     */
    protected $amount;

    function getId() {
        return $this->id;
    }

    function getAmount() {
        return $this->amount;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setAmount($amount) {
        $this->amount = $amount;
    }


}
