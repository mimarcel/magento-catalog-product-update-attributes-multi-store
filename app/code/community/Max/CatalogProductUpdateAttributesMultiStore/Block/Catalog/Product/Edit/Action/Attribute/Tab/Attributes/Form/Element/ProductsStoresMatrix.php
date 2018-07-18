<?php
class Max_CatalogProductUpdateAttributesMultiStore_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes_Form_Element_ProductsStoresMatrix
    extends Varien_Data_Form_Element_Abstract
{
    /** @var array */
    protected $products;
    /** @var array */
    protected $stores;
    /** @var array */
    protected $elements;
    /** @var string */
    protected $label;
    /** @var string */
    protected $class;

    public function __construct(
        array $products,
        array $stores,
        $elements,
        $config
    ) {
        parent::__construct(array());

        $this->products = $products;
        $this->stores = $stores;
        $this->elements = $elements;
        $this->label = isset($config['label']) ? $config['label'] : '';
        $this->class = isset($config['class']) ? $config['class'] : '';
    }

    public function getElementHtml()
    {
        $html = '<table id="' . $this->getId() . '" class="products-stores-matrix ' . $this->class . '">';
        $html .= $this->_getElementHtmlHeaderRow();
        $html .= $this->_getElementHtmlRows();

        $html .= '</table>';
        $html.= $this->getAfterElementHtml();

        return $html;
    }

    public function getLabel()
    {
        return $this->label;
    }

    protected function _getElementHtmlHeaderRow()
    {
        $html = '<tr>';

        $html .= '<th></th>';

        foreach ($this->stores as $store) {
            $html .= "<th>$store</th>";
        }

        $html .= '</tr>';

        return $html;
    }

    protected function _getElementHtmlRows()
    {
        $html = '';

        foreach ($this->products as $productId => $productLabel) {
            $html .= '<tr>';
            $html .= "<th>$productLabel</th>";
            foreach ($this->stores as $storeId => $storeLabel) {
                /** @var Varien_Data_Form_Element_Abstract $element */
                $element = $this->elements[$productId][$storeId];
                $html .= "<td>{$element->getElementHtml()}</td>";
            }
            $html .= '</tr>';

        }

        return $html;
    }
}
