<?php

namespace Grocery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use Zend\EventManager\EventInterface;
use Zend\Session\Container;

/*
 * Entities
 */
use Grocery\Entity\Product;
use Grocery\Entity\ProductFact;

class GroceryProductFactController extends AbstractActionController {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;

    public function __construct(EntityManager $doctrineService) {
        $this->em = $doctrineService;
    }

    public function indexAction() {
        $this->layout('layout/admin');
        $productFacts = $this->em->getRepository('Grocery\Entity\ProductFact')->findAll();

        return new ViewModel(
                array(
            'productFacts' => $productFacts,
                )
        );
    }

    public function addAction() {
        $this->layout('layout/admin');
        $productFact = new ProductFact();
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($productFact);

        $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\ProductFact'));
        $form->bind($productFact);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->em->persist($productFact);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Product weetje opgeslagen');
                $this->redirect()->toRoute('groceryProductFact');
            }
        }
        return new ViewModel(
                array(
            'form' => $form,
                )
        );
    }

    public function changeAction() {
        $this->layout('layout/admin');
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('groceryProductFact');
        }
        $productFact = $this->em
                ->getRepository('Grocery\Entity\ProductFact')
                ->findOneBy(array('productFactId' => $id));
        if (!$productFact) {
            return $this->redirect()->toRoute('groceryProductFact');
        }
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($productFact);

        $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\ProductFact'));
        $form->bind($productFact);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->em->persist($productFact);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Product weetje gewijzigd');
                $this->redirect()->toRoute('groceryProductFact');
            }
        }
        return new ViewModel(
                array(
            'form' => $form,
                )
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('groceryProductFact');
        }
        $productFact = $this->em
                ->getRepository('Grocery\Entity\ProductFact')
                ->findOneBy(array('productFactId' => $id));
        if (!$productFact) {
            return $this->redirect()->toRoute('groceryProductFact');
        }
        $this->em->remove($productFact);
        $this->em->flush();
        $this->flashMessenger()->addSuccessMessage('Product weetje verwijderen');
        return $this->redirect()->toRoute('groceryProductFact');
    }

}
