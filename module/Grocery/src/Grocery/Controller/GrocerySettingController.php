<?php

namespace Grocery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;

/*
 * Entities
 */
use Grocery\Entity\ProductGroup;

class GrocerySettingController extends AbstractActionController {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;
    protected $vhm;

    public function __construct(EntityManager $doctrineService, $vhm) {
        $this->em = $doctrineService;
        $this->vhm = $vhm;
    }

    public function indexAction() {
        $this->layout('layout/admin');
        $this->vhm->get('headScript')->appendFile('/js/timedropper.js');
        $this->vhm->get('headScript')->appendFile('/js/settings.js');
        $this->vhm->get('headLink')->appendStylesheet('/css/timedropper/timedropper.css');
        $settings = $this->em->getRepository('Grocery\Entity\GrocerySettings')->find(1);
        $supermarkets = $this->em->getRepository('Grocery\Entity\Supermarket')->findAll();
        $supermarket =  new \Grocery\Entity\Supermarket; 
        if (!is_object($settings)) {
            $settings = new \Grocery\Entity\GrocerySettings;
        }

        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($settings);
        $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\GrocerySettings'));
        $form->bind($settings);
        
        $formSupermarkets = $builder->createForm($supermarket);
        $formSupermarkets->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\Supermarket'));
        $formSupermarkets->bind($supermarket);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->em->persist($settings);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Settings opgeslagen');
            }
            
            $formSupermarkets->setData($this->getRequest()->getPost());
            if ($formSupermarkets->isValid()) {
                $this->em->persist($supermarket);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Supermarket opgeslagen');
            }
        }
        
        return new ViewModel(
                array(
            'settings' => $settings,
            'form' => $form,
            'supermarkets' => $supermarkets,
            'formSupermarkets' => $formSupermarkets
                )
        );
    }
    
    public function deleteSupermarketAction(){
        
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('grocerySettings');
        }
        $supermarket = $this->em
                ->getRepository('Grocery\Entity\Supermarket')
                ->findOneBy(array('id' => $id));
        if (!$supermarket) {
            return $this->redirect()->toRoute('grocerySettings');
        }
        $this->em->remove($supermarket);
        $this->em->flush();
        $this->flashMessenger()->addSuccessMessage('Supermarkt verwijderd');
        return $this->redirect()->toRoute('grocerySettings');
    }

}
