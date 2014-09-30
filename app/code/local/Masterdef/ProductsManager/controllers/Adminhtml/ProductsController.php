<?php

class Masterdef_ProductsManager_Adminhtml_ProductsController 
    extends Mage_Adminhtml_Controller_action
{

    public function indexAction()
    {
        $block = Mage::app()->getLayout()
                  ->createBlock('productsmanager/adminhtml_products') 
                  ->setTemplate('productsmanager/products.phtml');

        echo $block->toHtml();
    }

    public function saveAction()
    {
        $products = $this->getRequest()->getParam('products');
        foreach ($products as $productId => $productData) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $stockData = $product->getStockData();
            $stockData['qty'] = $productData['stock'];
            $stockData['is_in_stock'] = $stockData['qty'] ? 1 : 0;
            $product->setStockData($stockData);
            $product->save();
        }
    }
} 
