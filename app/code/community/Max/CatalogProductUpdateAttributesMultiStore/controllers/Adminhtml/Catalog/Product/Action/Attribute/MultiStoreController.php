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

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $attributesData = $this->getRequest()->getParam('attributes', array());
        $data = new Varien_Object();

        try {
            if ($attributesData) {
                foreach ($attributesData as $attributeCode => $storesValues) {
                    $attribute = Mage::getSingleton('eav/config')
                        ->getAttribute('catalog_product', $attributeCode);
                    if (!$attribute->getAttributeId()) {
                        unset($attributesData[$attributeCode]);
                        continue;
                    }
                    foreach ($storesValues as $storeId => $productsValues) {
                        foreach ($productsValues as $value) {
                            $data->setData($attributeCode, $value);
                            $attribute->getBackend()->validate($data);
                        }
                    }
                }
            }
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred while updating the product(s) attributes.'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
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
