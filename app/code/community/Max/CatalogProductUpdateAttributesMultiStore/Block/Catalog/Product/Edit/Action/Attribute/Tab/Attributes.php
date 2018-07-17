<?php

class Max_CatalogProductUpdateAttributesMultiStore_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
{
    protected $_products = null;
    protected $_stores = null;

    /**
     * Set Fieldset fields to Form
     *
     * Note: Same as parent method, but for every attribute we need to generate p*s fields
     * where    a = the number of products
     *          s = the number of stores
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()
                    ->getInputType()) && !in_array($attribute->getAttributeCode(),
                    $exclude) && ('media_image' != $inputType)) {
                $fieldType = $inputType;
                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $elements = array();
                foreach (array_keys($this->_getProducts()) as $productId) {
                    foreach (array_keys($this->_getStores()) as $storeId) {
                        $fieldId = "{$attribute->getAttributeCode()}_{$storeId}_{$productId}";
                        $fieldName = "{$attribute->getAttributeCode()}[{$storeId}][{$productId}]";
                        $element = $fieldset->addField($fieldId, $fieldType, array(
                                'name' => $fieldName,
                                'label' => $attribute->getFrontend()->getLabel(),
                                'class' => $attribute->getFrontend()->getClass(),
                                'required' => $attribute->getIsRequired(),
                                'note' => $attribute->getNote(),
                            ))->setEntityAttribute($attribute);

                        $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                        if ($inputType == 'select') {
                            $element->setValues($attribute->getSource()->getAllOptions(true, true));
                        } else {
                            if ($inputType == 'multiselect') {
                                $element->setValues($attribute->getSource()
                                    ->getAllOptions(false, true));
                                $element->setCanBeEmpty(true);
                            } else {
                                if ($inputType == 'date') {
                                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                                    $element->setFormat(Mage::app()
                                        ->getLocale()
                                        ->getDateFormatWithLongYear());
                                } else {
                                    if ($inputType == 'datetime') {
                                        $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                                        $element->setTime(true);
                                        $element->setStyle('width:50%;');
                                        $element->setFormat(Mage::app()
                                            ->getLocale()
                                            ->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                                    } else {
                                        if ($inputType == 'multiline') {
                                            $element->setLineCount($attribute->getMultilineCount());
                                        }
                                    }
                                }
                            }
                        }

                        $elements[$productId][$storeId] = $element;
                        $fieldset->removeField($fieldId);
                    }
                }

                // @todo Create block without `new`
                $matrix = new Max_CatalogProductUpdateAttributesMultiStore_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes_Form_Element_ProductsStoresMatrix(
                    $this->_getProducts(),
                    $this->_getStores(),
                    $elements,
                    $attribute->getFrontend()->getLabel()
                );
                $matrix->setId($attribute->getAttributeCode());
                $fieldset->addElement($matrix);
            }
        }
    }

    protected function _getProducts()
    {
        if ($this->_products === null) {
            $this->_products = array();

            $products = $this->getRequest()->getPost('product');
            if (!is_array($products)) {
                Mage::throwException('Invalid products variable');
            }
            foreach ($products as $product) {
                $this->_products[$product] = $product; // @todo get product name and other necessary product details
            }
        }

        return $this->_products;
    }

    protected function _getStores()
    {
        if ($this->_stores === null) {
            $this->_stores = array();
            foreach (Mage::app()->getStores(true) as $store) {
                $this->_stores[$store->getId()] = $store->getId() ? $store->getName() : 'Default';
            }
        }

        return $this->_stores;
    }

    /**
     * Overwrite to not add 'Change' box
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }

    protected function _afterToHtml($html)
    {
        // @todo Move style outside
        return parent::_afterToHtml($html)
            . <<<HTML
<style>
.products-stores-matrix input, textarea {
    padding: 0;
    font-size: 16px;
    margin: 0;
    width: 100%;
    height: 100%;
}
</style>
HTML;
    }
}
