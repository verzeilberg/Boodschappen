<?php

namespace Grocery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;

/*
 * Entities
 */

class GroceryCronJobController extends AbstractActionController {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;
    protected $gms;

    public function __construct(EntityManager $doctrineService, $gms) {
        $this->em = $doctrineService;
        $this->gms = $gms;
    }

    public function indexAction() {
        
        $id = 1;
        $settings = $this->em
                ->getRepository('Grocery\Entity\GrocerySettings')
                ->findOneBy(array('id' => $id));

        $orderFrequency = $settings->getOrderFrequency()->getValue();
        $currentDate = new \DateTime();

        
        switch ($orderFrequency) {
            case 1: //Dagelijks
                $orderTimeOfDay = (!empty($settings->getOrderDaily()) ? $settings->getOrderDaily() : '');
                $currentTime = $currentDate->format('H:m');
                if ($currentTime > $orderTimeOfDay) {
                    $this->sendOrderListAction();
                }

                break;
            case 2: // Weekelijks
                $orderDayOfWeek = $settings->getOrderWeekly()->getValue();
                $currentDayOfWeek = new \DateTime();
                $dayOfWeek = (int) date('N', strtotime($currentDate->format('l')));
                
                if ($orderDayOfWeek == $dayOfWeek) {
                    $this->sendOrderListAction();
                }
                
                break;
            case 3: //Maandelijks
                $orderDayOfMonth = $settings->getOrderMonthly()->getValue();
                $totalDaysInCurrentMonth = $currentDate->format('t');
                $currentDay = $currentDate->format('d');
                
                if($orderDayOfMonth > $totalDaysInCurrentMonth) {
                    $orderDayOfMonth = $totalDaysInCurrentMonth;
                }
                
                if ($orderDayOfMonth == $currentDay) {
                    $this->sendOrderListAction();
                }
                break;
        }

        //Send order reminder mail
        $this->sendOrderReminderAction();
        exit;
    }

    public function sendOrderReminderAction() {
        
        $users = $this->em
                ->getRepository('SiteUser\Entity\User')
                ->findAll();

        if (count($users) > 0) {
            foreach ($users AS $user) {
                $this->gms->sendOrderReminderMail($user);
            }
        }
    }

    public function sendOrderListAction() {

        $uri = $this->getRequest()->getUri();
        $preURL = $uri->getHost();
        $currentDate = new \DateTime();
        $year = (int) $currentDate->format("Y");
        $weekNumber = (int) $currentDate->format("W");
        //Get total of weeknumbers in year (52/53)
        $maxWeekNumber = $this->getIsoWeeksInYear($year);

        //Check if the weekNumber is same as total of weeks in a year
        if ($weekNumber == $maxWeekNumber) {
            $weekNumber = 1; //Next week is weeknr 1
            $year = $year + 1; //Next year
        } else {
            $weekNumber = $weekNumber + 1;
        }

        $productList = $this->em
                ->getRepository('Grocery\Entity\ProductList')
                ->findOneBy(array('year' => $year, 'weeknumber' => $weekNumber));

        if (count($productList) > 0) {
            $this->gms->sendOrderProductListMail($productList, $preURL);
        }
    }

    public function getIsoWeeksInYear($year) {
        $date = new \DateTime;
        $date->setISODate($year, 53);
        return ($date->format("W") === "53" ? 53 : 52);
    }

}
