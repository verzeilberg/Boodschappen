<?php

namespace Grocery\Service;

use Zend\Mail;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class groceryMailService implements groceryMailServiceInterface {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;
    protected $config;
    protected $serverUrl;
    protected $request;

    public function __construct($em, $config, $serverUrl, $request) {
        $this->em = $em;
        $this->config = $config;
        $this->serverUrl = $serverUrl;
        $this->request = $request;
    }

    public function sendOrderReminderMail($addressee = NULL) {
        if (!empty($addressee) && filter_var($addressee->getEmail(), FILTER_VALIDATE_EMAIL)) {


            $baseurl = $this->getBaseUrl();
            $config = $this->getConfig();
            $email_template = 'module/Grocery/' . $config['gocerySettings']['orderReminder']['reminder_order_mail_template'];
            //Sender information
            $mail_sender_email = $config['gocerySettings']['orderReminder']['mail_sender_email'];
            $mail_sender_name = $config['gocerySettings']['orderReminder']['mail_sender_name'];
            //Reply infomrtaion
            $mail_reply_email = $config['gocerySettings']['orderReminder']['mail_reply_email'];
            $mail_reply_name = $config['gocerySettings']['orderReminder']['mail_reply_name'];
            //Mail subject
            $mail_subject = 'Herinnering boodschappen';
            //Name of addressee
            $name = $addressee->getDisplayName();
            //URL of productlist
            $currentDate = new \DateTime();
            $year = (int) $currentDate->format("Y");
            $weekNumber = (int) $currentDate->format("W");
            //Get total of weeknumbers in year (52/53)
            $maxWeekNumber = $this->getIsoWeeksInYear($year);

            //Check if the weekNumber is same as total of weeks in a year
            if ($weekNumber == $maxWeekNumber) {
                $weekNumber = 1; //Next week is weeknr 1
            } else {
                $weekNumber = $weekNumber + 1;
            }

            if ($productList) {

                $url = $baseurl . '/productList/detail/' . $productList->getId();

                ob_start();
                require($email_template);
                $email_body = ob_get_clean();

                $mail = new Mail\Message();
                $mail->setEncoding("UTF-8");

                $html = new MimePart($email_body);
                $html->type = "text/html";

                $body = new MimeMessage();
                $body->setParts(array($html));

                $mail->setBody($body);
                $mail->setFrom($mail_sender_email, $mail_sender_name);
                $mail->addReplyTo($mail_reply_email, $mail_reply_name);
                $mail->addTo($addressee->getEmail(), $addressee->getDisplayName());
                $mail->setSubject($mail_subject);

                $transport = new Mail\Transport\Sendmail();
                $transport->send($mail);
            }
        }
    }

    /**
     *
     * Send grocery list
     *
     * @param       productList  $productList The object to create Blog url from
     * @param       preURL  $preURL url used in template
     * @return      void
     *
     */
    public function sendOrderProductListMail($productList = NULL, $preURL = NULL) {

        if (!empty($productList)) {

            $settings = $this->em
                    ->getRepository('Grocery\Entity\GrocerySettings')
                    ->findOneBy(array('id' => 1));

            $baseurl = $this->getBaseUrl();
            $config = $this->getConfig();
            $email_template = 'module/Grocery/' . $config['gocerySettings']['orderReminder']['product_order_list_mail_template'];
            //Sender information
            $mail_sender_email = $settings->getMailSenderEmail();
            if (empty($mail_sender_email)) {
                $mail_sender_email = $config['gocerySettings']['orderReminder']['mail_sender_email'];
            }

            $mail_sender_name = $name = $settings->getMailSenderName();
            if (empty($mail_sender_name)) {
                $mail_sender_name = $name = $config['gocerySettings']['orderReminder']['mail_sender_name'];
            }
            //Reply information

            $mail_reply_email = $settings->getMailReplyEmail();
            if (empty($mail_reply_email)) {
                $mail_reply_email = $config['gocerySettings']['orderReminder']['mail_reply_email'];
            }

            $mail_reply_name = $settings->getMailReplyName();
            if (empty($mail_reply_email)) {
                $mail_reply_name = $config['gocerySettings']['orderReminder']['mail_reply_name'];
            }
            //Mail subject
            $mail_subject = 'Boodschappenlijst';

            ob_start();
            include($email_template);
            $email_body = ob_get_clean();

            $mail = new Mail\Message();
            $mail->setEncoding("UTF-8");

            $html = new MimePart($email_body);
            $html->type = "text/html";

            $body = new MimeMessage();
            $body->setParts(array($html));

            $mail->setBody($body);
            $mail->setFrom($mail_sender_email, $mail_sender_name);
            $mail->addReplyTo($mail_reply_email, $mail_reply_name);
            $mail->addTo($mail_sender_email, 'Sander');
            $mail->setSubject($mail_subject);

            $transport = new Mail\Transport\Sendmail();
            $transport->send($mail);
        }
    }

    public function sendSuggestionMail() {

        $settings = $this->em
                ->getRepository('Grocery\Entity\GrocerySettings')
                ->findOneBy(array('id' => 1));

        $baseurl = $this->getBaseUrl();
        $config = $this->getConfig();
        $email_template = 'module/Grocery/' . $config['gocerySettings']['orderReminder']['product_suggestion_template'];
        //Sender information
        $mail_sender_email = $settings->getMailSenderEmail();
        if (empty($mail_sender_email)) {
            $mail_sender_email = $config['gocerySettings']['orderReminder']['mail_sender_email'];
        }

        $mail_sender_name = $name = $settings->getMailSenderName();
        if (empty($mail_sender_name)) {
            $mail_sender_name = $name = $config['gocerySettings']['orderReminder']['mail_sender_name'];
        }
        //Reply information

        $mail_reply_email = $settings->getMailReplyEmail();
        if (empty($mail_reply_email)) {
            $mail_reply_email = $config['gocerySettings']['orderReminder']['mail_reply_email'];
        }

        $mail_reply_name = $settings->getMailReplyName();
        if (empty($mail_reply_email)) {
            $mail_reply_name = $config['gocerySettings']['orderReminder']['mail_reply_name'];
        }
        //Mail subject
        $mail_subject = 'Boodschappen: Er is een suggestie gedaan voor een nieuw product';
        //URL to suggestion page
        $url = $baseurl . '/product/productSuggestions';
        
        ob_start();
        include($email_template);
        $email_body = ob_get_clean();

        $mail = new Mail\Message();
        $mail->setEncoding("UTF-8");

        $html = new MimePart($email_body);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));
        
        $mail->setBody($body);
        $mail->setFrom($mail_sender_email, $mail_sender_name);
        $mail->addReplyTo($mail_reply_email, $mail_reply_name);
        $mail->addTo($mail_sender_email, 'Sander');
        $mail->setSubject($mail_subject);

        $transport = new Mail\Transport\Sendmail();
        $transport->send($mail);
    }

    public function getBaseUrl() {
        $helper = $this->getServerUrl();
        return $helper->__invoke($this->request->getBasePath());
    }

    public function getConfig() {
        return $this->config;
    }

    public function getServerUrl() {
        return $this->serverUrl;
    }

    public function getIsoWeeksInYear($year) {
        $date = new \DateTime;
        $date->setISODate($year, 53);
        return ($date->format("W") === "53" ? 53 : 52);
    }

}
