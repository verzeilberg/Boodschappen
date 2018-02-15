<?php
namespace Grocery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Entity\Label;

/*
 * Entities
 */

/**
 * Product
 *
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $productId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Annotation\Options({
     * "label": "Product nummer",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Product nummer"})
     */
    protected $productNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "Product naam",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $name;

    /**
     * @ORM\Column(type="text", length=255, nullable=false)
     * * @Annotation\Options({
     * "label": "Omschrijving",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Omschrijving"})
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Options({
     * "label": "Product prijs",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Prijs", "min":"0", "step":"any"})
     */
    protected $price;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     * @Annotation\Exclude()
     */
    protected $indexString;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\File")
     * * @Annotation\Options({
     * "label": "Product image",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     */
    private $productImage;

    /**
     * Many Products have Many ProductGroups.
     * @ORM\ManyToMany(targetEntity="ProductGroup", inversedBy="products")
     * @ORM\JoinTable(name="products_groups")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectMultiCheckbox")
     * @Annotation\Options({
     * "target_class":"Grocery\Entity\ProductGroup",
     * "property": "name",
     * "label": "Product groep(en)",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":""})
     */
    private $productGroups;

    /**
     * One Product has Many ProductListDetails.
     * @ORM\OneToMany(targetEntity="ProductListDetail", mappedBy="product")
     */
    private $productListDetails;

    /**
     * Many products have Many Images.
     * @ORM\ManyToMany(targetEntity="ProductImage")
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     * @ORM\JoinTable(name="product_images",
     *      joinColumns={@ORM\JoinColumn(name="productId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="imageId", referencedColumnName="id", unique=true)}
     *      )
     */
    private $productImages;

    /**
     * One Product has Many Product facts.
     * @ORM\OneToMany(targetEntity="ProductFact", mappedBy="product")
     */
    private $productFacts;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Label")
     * @ORM\JoinColumn(name="orderFrequenty", referencedColumnName="id")
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
    protected $orderFrequenty;

    public function __construct() {
        $this->productGroups = new ArrayCollection();
        $this->productListDetails = new ArrayCollection();
        $this->productImages = new ArrayCollection();
        $this->productFacts = new ArrayCollection();
    }

    public function addProductGroups($productGroups) {
        foreach ($productGroups as $productGroup) {
            $this->productGroups->add($productGroup);
        }
    }

    public function removeProductGroups($productGroups) {
        foreach ($productGroups as $productGroup) {
            $this->productGroups->removeElement($productGroup);
        }
    }

    function getName() {
        return $this->name;
    }

    function getDescription() {
        return $this->description;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function getProductNumber() {
        return $this->productNumber;
    }

    function getProductGroups() {
        return $this->productGroups;
    }

    function setProductNumber($productNumber) {
        $this->productNumber = $productNumber;
    }

    function setProductGroups($productGroups) {
        $this->productGroups = $productGroups;
    }

    function getProductListDetails() {
        return $this->productListDetails;
    }

    function setProductListDetails($productListDetails) {
        $this->productListDetails = $productListDetails;
    }

    function getProductImage() {
        return $this->productImage;
    }

    function setProductImage($productImage) {
        $this->productImage = $productImage;
    }

    function getProductImages($imageType = NULL) {
        return $this->productImages;
    }

    function setProductImages($productImages) {
        $this->productImages = $productImages;
    }

    public function addProductImage(ProductImage $productImage) {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages->add($productImage);
        }
        return $this;
    }

    public function removeProductImage(ProductImage $productImage) {
        if ($this->productImages->contains($productImage)) {
            $this->productImages->removeElement($productImage);
        }
        return $this;
    }

    function getProductId() {
        return $this->productId;
    }

    function setProductId($productId) {
        $this->productId = $productId;
    }

    function getPrice() {
        return $this->price;
    }

    function setPrice($price) {
        $this->price = $price;
    }

    function getIndexString() {
        return $this->indexString;
    }

    function setIndexString($indexString) {
        $this->indexString = $indexString;
    }

    function getProductFacts() {
        return $this->productFacts;
    }

    function setProductFacts($productFacts) {
        $this->productFacts = $productFacts;
    }

    function getOrderFrequenty() {
        return $this->orderFrequenty;
    }

    function setOrderFrequenty($orderFrequenty) {
        $this->orderFrequenty = $orderFrequenty;
    }


}
