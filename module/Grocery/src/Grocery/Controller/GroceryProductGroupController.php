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

class GroceryProductGroupController extends AbstractActionController {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $em;

    public
            function __construct(EntityManager $doctrineService) {
        $this->em = $doctrineService;
    }

    public function indexAction() {
        $this->layout('layout/admin');
        $productGroups = $this->em->getRepository('Grocery\Entity\ProductGroup')->findAll();

        return new ViewModel(
                array(
            'productGroups' => $productGroups,
                )
        );
    }

    public function addAction() {
        $this->layout('layout/admin');
        $productGroup = new ProductGroup();
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($productGroup);

        $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\ProductGroup'));
        $form->bind($productGroup);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->em->persist($productGroup);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Product groep opgeslagen');
                $this->redirect()->toRoute('groceryProductGroup');
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
            return $this->redirect()->toRoute('groceryProductGroup');
        }
        $productGroup = $this->em
                ->getRepository('Grocery\Entity\ProductGroup')
                ->findOneBy(array('id' => $id));
        if (!$productGroup) {
            return $this->redirect()->toRoute('groceryProductGroup');
        }
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($productGroup);

        $form->setHydrator(new DoctrineHydrator($this->em, 'Grocery\Entity\ProductGroup'));
        $form->bind($productGroup);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->em->persist($productGroup);
                $this->em->flush();
                $this->flashMessenger()->addSuccessMessage('Product groep gewijzigd');
                $this->redirect()->toRoute('groceryProductGroup');
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
            return $this->redirect()->toRoute('groceryProductGroup');
        }
        $productGroup = $this->em
                ->getRepository('Grocery\Entity\ProductGroup')
                ->findOneBy(array('id' => $id));
        if (!$productGroup) {
            return $this->redirect()->toRoute('groceryProduct');
        }
        $this->em->remove($productGroup);
        $this->em->flush();
        $this->flashMessenger()->addSuccessMessage('Product groep verwijderen');
        return $this->redirect()->toRoute('groceryProductGroup');
    }

    public function detailAction() {
        $this->layout('layout/admin');
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('groceryProductGroup');
        }
        $productGroup = $this->em
                ->getRepository('Grocery\Entity\ProductGroup')
                ->findOneBy(array('id' => $id));
        if (!$productGroup) {
            return $this->redirect()->toRoute('groceryProductGroup');
        }
        return new ViewModel(
                array(
            'productGroup' => $productGroup,
                )
        );
    }

}
