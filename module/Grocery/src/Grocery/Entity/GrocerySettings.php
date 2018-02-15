<?php

namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Club
 *
 * @ORM\Entity
 * @ORM\Table(name="grocerysettings")
 */
class GrocerySettings {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Label")
     * @ORM\JoinColumn(name="orderFrequency", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "empty_option": "---",
     * "target_class":"Application\Entity\Label",
     * "property": "label",
     * "label": "Order frequentie",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"},
     * "find_method":{"name": "findBy","params": {"criteria":{"labelsetid": "1"},"orderBy":{"sortorder": "ASC","label":"ASC"}}}
     * })
     * @Annotation\Attributes({"class": "form-control"})
     */
    protected $orderFrequency;

    /**
     * @ORM\Column(type="text", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "Op welk tijdstip besteld u de boodschappen?",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Time", "id":"time"})
     */
    protected $orderDaily;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Label")
     * @ORM\JoinColumn(name="orderWeekly", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "empty_option": "---",
     * "target_class":"Application\Entity\Label",
     * "property": "label",
     * "label": "Welke dag van de week besteld u de boodschappen?",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"},
     * "find_method":{"name": "findBy","params": {"criteria":{"labelsetid": "2"},"orderBy":{"sortorder": "ASC","label":"ASC"}}}
     * })
     * @Annotation\Attributes({"class": "form-control"})
     */
    protected $orderWeekly;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Label")
     * @ORM\JoinColumn(name="orderMonthly", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "empty_option": "---",
     * "target_class":"Application\Entity\Label",
     * "property": "label",
     * "label": "Welke dag van de maand besteld u de boodschappen?",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"},
     * "find_method":{"name": "findBy","params": {"criteria":{"labelsetid": "3"},"orderBy":{"sortorder": "ASC","label":"ASC"}}}
     * })
     * @Annotation\Attributes({"class": "form-control"})
     */
    protected $orderMonthly;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "E-mail verstuurder e-mail",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"e-mail"})
     */
    protected $mailSenderEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "E-mail verstuurder naam",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $mailSenderName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "E-mail reply adres",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"E-mail"})
     */
    protected $mailReplyEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "E-mail reply naam",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $mailReplyName;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     * * @Annotation\Options({
     * "label": "Budget per werknemer/per maand",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"budget", "step":"any", "min":"1" })
     */
    protected $defaultBudget;
    
        /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Label")
     * @ORM\JoinColumn(name="reminderMailSendDays", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\MultiCheckbox")
     * @Annotation\Options({
     * "empty_option": "---",
     * "target_class":"Application\Entity\Label",
     * "property": "label",
     * "label": "Op welke dagen moeten er herrineringsmail worden verstuurd?",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"},
     * "find_method":{"name": "findBy","params": {"criteria":{"labelsetid": "4"},"orderBy":{"sortorder": "ASC","label":"ASC"}}}
     * })
     * @Annotation\Attributes({"class": "form-control"})
     */
    protected $reminderMailSendDays;

    function getId() {
        return $this->id;
    }

    function getOrderFrequency() {
        return $this->orderFrequency;
    }

    function getOrderDay() {
        return $this->orderDay;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setOrderFrequency($orderFrequency) {
        $this->orderFrequency = $orderFrequency;
    }

    function setOrderDay($orderDay) {
        $this->orderDay = $orderDay;
    }

    function getOrderDaily() {
        return $this->orderDaily;
    }

    function getOrderWeekly() {
        return $this->orderWeekly;
    }

    function getOrderMonthly() {
        return $this->orderMonthly;
    }

    function setOrderDaily($orderDaily) {
        $this->orderDaily = $orderDaily;
    }

    function setOrderWeekly($orderWeekly) {
        $this->orderWeekly = $orderWeekly;
    }

    function setOrderMonthly($orderMonthly) {
        $this->orderMonthly = $orderMonthly;
    }

    function getMailSenderEmail() {
        return $this->mailSenderEmail;
    }

    function getMailSenderName() {
        return $this->mailSenderName;
    }

    function getMailReplyEmail() {
        return $this->mailReplyEmail;
    }

    function getMailReplyName() {
        return $this->mailReplyName;
    }

    function setMailSenderEmail($mailSenderEmail) {
        $this->mailSenderEmail = $mailSenderEmail;
    }

    function setMailSenderName($mailSenderName) {
        $this->mailSenderName = $mailSenderName;
    }

    function setMailReplyEmail($mailReplyEmail) {
        $this->mailReplyEmail = $mailReplyEmail;
    }

    function setMailReplyName($mailReplyName) {
        $this->mailReplyName = $mailReplyName;
    }
    
    function getDefaultBudget() {
        return $this->defaultBudget;
    }

    function setDefaultBudget($defaultBudget) {
        $this->defaultBudget = $defaultBudget;
    }

    function getReminderMailSendDays() {
        return $this->reminderMailSendDays;
    }

    function setReminderMailSendDays($reminderMailSendDays) {
        $this->reminderMailSendDays = $reminderMailSendDays;
    }



}
