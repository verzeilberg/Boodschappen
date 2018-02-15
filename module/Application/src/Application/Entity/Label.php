<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Type of device procedure
 *
 * @ORM\Entity
 * add index to the VALUE column so the field can be used as foreign key
 * @ORM\Table(name="_dataset_labelset",
 *    uniqueConstraints={@ORM\UniqueConstraint(name="label_unique",
 *    columns={"LABELSET_ID", "VALUE"})}
 * )
 * @property int $value
 * @property int $labelsetid
 * @property string $label
 * @property int $sortorder
 */
class Label
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned":true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="labelset_id", type="integer");
     * @Annotation\Options({"label": "Labelset"})
     */
    protected $labelsetid;
    
    /**
     * @ORM\Column(name="value", type="integer");
     * @Annotation\Options({"label": "Value"})
     */
    protected $value;

    /**
     * @ORM\Column(name="label", type="string", length=255)
     * @Annotation\Options({"label": "Label"})
     */
    protected $label;

    /**
     * @ORM\Column(name="dipr_value", type="string", length=3, nullable=true);
     * @Annotation\Options({"label": "DIPR value"})
     */
    protected $diprvalue;
    
    /**
     * @ORM\Column(name="sortorder", type="integer", options={"default" = 0}))
     * @Annotation\Options({"label": "Volgorde"})
     */
    protected $sortorder;

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setLabelsetid($value)
    {
        $this->labelsetid = $value;
        return $this;
    }
    
    public function getLabelsetid()
    {
        return $this->labelsetid;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
    
    public function getLabel()
    {
        return $this->label;
    }

    public function setDiprvalue($value)
    {
        $this->diprvalue = $value;
        return $this;
    }
    
    public function getDiprvalue()
    {
        return $this->diprvalue;
    }

    public function setSortorder($value)
    {
        $this->sortorder = $value;
        return $this;
    }
    
    public function getSortorder()
    {
        return $this->sortorder;
    }
    
    public function __toString()
    {
        return $this->label;
    }
}
