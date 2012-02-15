<?php

class Masterdef_ProductsManager_Adminhtml_IndexController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
             ->_setActiveMenu('catalog/categories')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Products Manager'), 
                    Mage::helper('adminhtml')->__('Catalog / Products Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
             ->renderLayout();
    }


    public function saveAction()
    {
        Masterdef_Log::I()->log('saveAction_call', $_POST);
        $data = $this->getRequest()->getParam('data');
        $data = explode('-', $data);

        $categoryIds = $data;

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => $categoryIds))
            ->addAttributeToSelect('start_date_sale')
            ->addAttributeToSelect('display_date')
            ->addAttributeToSelect('name')
            ->addExpressionAttributeToSelect('ze_date', 
                'IF({{display_date}} IS NOT NULL, {{display_date}}, {{start_date_sale}})', 
                array('display_date', 'start_date_sale')
            )
            ->setOrder('ze_date', 'DESC');

        $saleById = array();
        $saleByPosition = array();

        foreach($collection as $category) {
            $saleByPosition[] = $category;
            $saleById[$category->getId()] = $category;
        }

        $firstCategory = $saleByPosition[0];
        $displayDate = new Zend_Date($firstCategory->getZeDate(), 'YYYY-MM-dd HH:mm:ss');

        $res = Mage::getSingleton('core/resource');
        $con = $res->getConnection('core_write');

        $position = 0;
        foreach($categoryIds as $postion => $id) if ($id) {
            $position++;
            try {
                Mage::getModel('catalog/category')->load($id)
                    ->setPosition($position)
                    ->setDisplayDate($displayDate->get('YYYY-MM-dd HH:mm:ss'))
                    ->save();
                $displayDate->sub(1, Zend_Date::MINUTE);
            } catch(Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }

        Mage::getModel('salessort/observer')->salesSort();

        $this->_redirect('*/*');
    }
}

