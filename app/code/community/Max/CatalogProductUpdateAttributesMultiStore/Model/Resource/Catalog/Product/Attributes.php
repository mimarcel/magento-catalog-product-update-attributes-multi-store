<?php
class Max_CatalogProductUpdateAttributesMultiStore_Model_Resource_Catalog_Product_Attributes extends Mage_Core_Model_Resource
{
    /**
     * @param array $productsIds
     * @param Mage_Catalog_Model_Resource_Eav_Attribute[] $attributes
     * @param array $storesIds
     *
     * @return array
     */
    public function getValues($productsIds, $attributes, $storesIds)
    {
        $attributesIds = $this->_getAttributesIds($attributes);
        $tables = $this->_getTables($attributes);

        $values = array();
        foreach ($tables as $table) {
            $select = $this->_getConnection()->select()
                ->from(array('main' => $table), array('attribute_id', 'store_id', 'entity_id', 'value'))
                ->where('entity_id in (?)', $productsIds)
                ->where('attribute_id in (?)', $attributesIds)
                ->where('store_id in (?)', $storesIds);

            $values = array_merge($values, $this->_getConnection()->fetchAll($select));
        }

        return $values;
    }

    protected function _getConnection()
    {
        return $this->getConnection('core_read');
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute[] $attributes
     *
     * @return array
     */
    protected function _getAttributesIds($attributes)
    {
       return array_map(
           function($attribute) { return $attribute->getId();},
           $attributes
       );
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute[] $attributes
     *
     * @return array
     */
    private function _getTables($attributes)
    {
        $tables = array();
        foreach ($attributes as $attribute) {
            $tables[] = $attribute->getBackendTable();
        }

        return array_unique($tables);
    }
}