<?php
class Max_CatalogProductUpdateAttributesMultiStore_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes_Form_Element_ProductsStoresMatrix
    extends Varien_Data_Form_Element_Abstract
{
    /** @var array */
    protected $rows;
    /** @var array */
    protected $columns;
    /** @var array */
    protected $elements;
    /** @var string */
    protected $label;

    public function __construct(
        array $rows, array $columns,
        $elements,
        $label
    ) {
        parent::__construct(array());

        $this->rows = $rows;
        $this->columns = $columns;
        $this->elements = $elements;
        $this->label = $label;
    }

    public function getElementHtml()
    {
        $html = '<table class="products-stores-matrix">';
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

        foreach ($this->columns as $column) {
            $html .= "<th>$column</th>";
        }

        $html .= '</tr>';

        return $html;
    }

    protected function _getElementHtmlRows()
    {
        $html = '';

        foreach ($this->rows as $rowId => $row) {
            $html .= '<tr>';
            $html .= "<th>$row</th>";
            foreach ($this->columns as $columnId => $column) {
                /** @var Varien_Data_Form_Element_Abstract $element */
                $element = $this->elements[$rowId][$columnId];
                $html .= "<td>{$element->getElementHtml()}</td>";
            }
            $html .= '</tr>';

        }

        return $html;
    }
}
