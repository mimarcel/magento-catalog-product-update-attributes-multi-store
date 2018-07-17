<?php

class Max_CatalogProductUpdateAttributesMultiStore_Model_Observer
{
    public function addButtonsInProductsGrid(Varien_Event_Observer $event)
    {
        $block = $event->getData('block');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')) {
                $block->getMassactionBlock()->addItem('attributes_multiStore', array(
                    'label' => Mage::helper('catalog')->__('Update Attributes Multi-Store'),
                    'url' => $block->getUrl(
                        '*/catalog_product_action_attribute_multiStore/edit',
                        array('_current' => true)),
                ));
            }
        }
    }
}
