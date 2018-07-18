<?php

class Max_CatalogProductUpdateAttributesMultiStore_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
{
    protected $_products = null;
    protected $_stores = null;

    /**
     * Set Fieldset fields to Form
     *
     * Note: Same as parent method, but for every attribute we need to generate p*s fields instead of just 1 field
     * where    p = the number of products
     *          s = the number of stores
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute[] $attributes
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = array())
    {
        $this->_addElementTypes($fieldset);
        $attributes = $this->_filterAttributes($attributes, $exclude);
        $values = $this->_loadProductsValues($attributes);

        foreach ($attributes as $attribute) {
            $inputType = $attribute->getFrontend()->getInputType();
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

                    $value = isset($values[$productId][$attribute->getId()][$storeId])
                        ? $values[$productId][$attribute->getId()][$storeId]
                        : null;
                    if ($value === null) {
                        $value = isset($values[$productId][$attribute->getId()][0])
                            ? $values[$productId][$attribute->getId()][0]
                            : null;
                    }
                    $element->setValue($value);
                    $elements[$productId][$storeId] = $element;
                    $fieldset->removeField($fieldId);
                }
            }

            // @todo Create block without `new`
            $matrix = new Max_CatalogProductUpdateAttributesMultiStore_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes_Form_Element_ProductsStoresMatrix(
                $this->_getProducts(),
                $this->_getStores(),
                $elements,
                array(
                    'label' => $attribute->getFrontend()->getLabel(),
                    'class' => 'type_' . $fieldType,
                )
            );
            $matrix->setId('matrix_' . $attribute->getAttributeCode());
            $fieldset->addElement($matrix);
        }
    }

    /**
     * Copied from parent method parent::_setFieldset.
     * @todo Consider giving up on dependencies from parent methods.
     *
     * @param $attributes
     * @param $exclude
     *
     * @return array
     */
    protected function _filterAttributes($attributes, $exclude)
    {
        $result = array();
        foreach ($attributes as $key => $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()->getInputType())
                && !in_array($attribute->getAttributeCode(), $exclude)
                && ('media_image' != $inputType)) {
                $result[$key] = $attribute;
            }
        }

        return $result;
    }

    protected function _getProducts()
    {
        if ($this->_products === null) {
            $products = $this->_getHelper()->getProductIds();
            if (!is_array($products)) {
                Mage::throwException('Invalid products variable');
            }

            $this->_products = array();
            foreach ($products as $product) {
                $this->_products[$product] = Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue($product, 'sku');
                // @todo improve retrieving name in one query (hint: use collection)
            }
        }

        return $this->_products;
    }

    /**
     * @return Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute
     */
    protected function _getHelper()
    {
        return Mage::helper('adminhtml/catalog_product_edit_action_attribute');
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute[] $attributes
     *
     * @return array
     */
    protected function _loadProductsValues($attributes)
    {
        /** @var Max_CatalogProductUpdateAttributesMultiStore_Model_Resource_Catalog_Product_Attributes $resource */
        $resource = Mage::getResourceSingleton('catalogProductUpdateAttributesMultiStore/catalog_product_attributes');
        $productsValues = $resource->getValues(
            array_keys($this->_getProducts()),
            $attributes,
            array_keys($this->_getStores())
        );

        $values = array();
        foreach ($productsValues as $productValue) {
            $values[$productValue['entity_id']][$productValue['attribute_id']][$productValue['store_id']] = $productValue['value'];
        }

        return $values;
    }

    protected function _getStores()
    {
        // @todo When attribute is per scope website, show only a website value
        // @todo When attribute is per scope global, show only a global value
        // @todo Show attribute scope in frontend
        if ($this->_stores === null) {
            $this->_stores = array();
            foreach (Mage::app()->getStores(true) as $store) {
                $this->_stores[$store->getId()] = $store->getId()
                    ? $store->getName()
                    : $this->__('Default');
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
.products-stores-matrix {
    border-spacing: 0;
}

.products-stores-matrix td {
    border-spacing: 0;
    width: 130px;
}

.products-stores-matrix input, .products-stores-matrix textarea, .products-stores-matrix select {
    padding: 0;
    font-size: 16px;
    margin: 0;
    height: 20px;
    width: 130px;
    border: 1px solid #f3f0f0;
}

.products-stores-matrix textarea {
    height: 40px;
}
</style>
HTML;
    }
}
