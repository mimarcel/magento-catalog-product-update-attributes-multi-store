<?php
/**
 * @see Mage_Adminhtml_Catalog_Product_Action_AttributeController
 */
class Max_CatalogProductUpdateAttributesMultiStore_Adminhtml_Catalog_Product_Action_Attribute_MultiStoreController
    extends Mage_Adminhtml_Controller_Action
{
    public function editAction()
    {
        try {
            $this->_validateProducts();
        } catch (Mage_Core_Exception $ex) {
            $this->_getSession()->addError($ex->getMessage());
            $this->_redirect('*/catalog_product/', array('_current'=>true));
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Validate selection of products for massupdate
     */
    protected function _validateProducts()
    {
        $productIds = $this->_getHelper()->getProductIds();
        if (!is_array($productIds)) {
            Mage::throwException($this->__('Please select products for attributes update'));
        } else if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
            Mage::throwException($this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.'));
        }
    }

    /**
     * @return Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute
     */
    protected function _getHelper()
    {
        return Mage::helper('adminhtml/catalog_product_edit_action_attribute');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes');
    }
}
