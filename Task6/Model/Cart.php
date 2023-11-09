<?php

namespace Speroteck\Task6\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use phpDocumentor\Reflection\Types\Boolean;

class Cart
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Boolean $skip
     */
    public $skip;

    /**
     * Cart constructor.
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }
    public function selectProduct()
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->getSelect()->orderRand();
        $productCollection->addAttributeToFilter('type_id', 'simple');

        $product = $productCollection->getFirstItem();
        return $product;
    }

    public function beforeAddProduct(
        \Magento\Checkout\Model\Cart $subject,
        $productInfo,
        $requestInfo = null
    ) {

        // Ver 1
//         $product = $this->selectProduct();
//         $qty = is_array($requestInfo) ? $requestInfo['qty'] ?? 1 : 1;
//         $subject->getQuote()->addProduct($product, $qty * 2);
        // ---------------------
        // Ver 2
        if (!$this->skip) {
            $product = $this->selectProduct();
            $this->skip=true;
            $subject->addProduct($product, $requestInfo);
        }
        // ---------------------

        $qty = is_array($requestInfo) ? $requestInfo['qty'] ?? 1 : 1;
        $requestInfo['qty'] = $qty * 2; // increasing quantity on 2

        return [$productInfo, $requestInfo];
    }
}

// Drafts

//// Ver 1
//$productCollection = $this->productCollectionFactory->create();
//// $productCollection->getSelect()->order(new \Zend_Db_Expr('RAND()'));
//$productCollection->getSelect()->orderRand();
//$productCollection->addAttributeToFilter('type_id', 'simple');
//
//// $productCollection->addAttributeToSelect('*');
//// $productCollection->addAttributeToSelect('name', true);
//// $productCollection->addAttributeToSelect('description', true);
//// die((string)$productCollection->getSelect());
//$product = $productCollection->getFirstItem();
//
//// ------------
//// Algorithm
//// 2) $quoteModel = $subject->getQuote();
//// 2) $quoteModel->addProduct($product2);
//// die((string)$productCollection->getSelect());
//
//// main code
//// $qty = is_array($requestInfo) ? $requestInfo['qty'] ?? 1 : 1;
//// $subject->getQuote()->addProduct($product, $qty * 2);
//// ------------
//
//// Ver 2
//if (!$this->skip) {
//
//    $productCollection = $this->productCollectionFactory->create();
//    $productCollection->getSelect()->orderRand();
//    $productCollection->addAttributeToFilter('type_id', 'simple');
//    $product = $productCollection->getFirstItem();
//
//    $this->skip=true;
//    $subject->addProduct($product, $requestInfo);
//}
//// $product2 = ...
//// 1) if (!$this->skip) {
//// 1)     $this->skip=true;
//// 1)     $subject->addProduct($product2);
//// 1) }
