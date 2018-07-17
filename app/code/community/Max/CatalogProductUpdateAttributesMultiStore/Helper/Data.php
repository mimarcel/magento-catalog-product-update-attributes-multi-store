<?php
class Max_CatalogProductUpdateAttributesMultiStore_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PRODUCTS_GRID_REMOVE_CORE_BUTTON = 'catalog/update_attributes_multi_store/disable_core_update_attributes';

    /**
     * @return string
     */
    public function isDisableCoreUpdateAttributes()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRODUCTS_GRID_REMOVE_CORE_BUTTON);
    }
}
