<?php

namespace Grocery\Service;

interface groceryMailServiceInterface {

    /**
     * Should return a set of all blog posts that we can iterate over. Single entries of the array are supposed to be
     * implementing \Blog\Model\PostInterface
     *
     * @return array|years[]
     */
    public function sendOrderReminderMail($addressee = NULL);
    
    public function getBaseUrl();
    
    public function getConfig();
    
    public function getServerUrl();
    
    public function getIsoWeeksInYear($year);

}
