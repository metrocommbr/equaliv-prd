<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\DataObject;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field\Renderer\Attributes;
use Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field\Renderer\Conditions;
use Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field\Renderer\ProductTypes;

/**
 * Class Filters
 *
 * @package Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field
 */
class Filters extends AbstractFieldArray
{

    /**
     * @var Attributes
     */
    private $attributeRenderer;
    /**
     * @var Conditions
     */
    private $conditionRenderer;
    /**
     * @var ProductTypes
     */
    private $productTypeRenderer;

    /**
     * Render block.
     */
    public function _prepareToRender()
    {
        $this->addColumn('attribute', [
            'label'    => __('Attribute'),
            'renderer' => $this->getAttributeRenderer()
        ]);
        $this->addColumn('condition', [
            'label'    => __('Condition'),
            'renderer' => $this->getConditionRenderer()
        ]);
        $this->addColumn('value', [
            'label' => __('Value'),
        ]);
        $this->addColumn('product_type', [
            'label'    => __('Apply To'),
            'renderer' => $this->getProductTypeRenderer()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface|Renderer\Attributes
     */
    public function getAttributeRenderer()
    {
        try {
            if (!$this->attributeRenderer) {
                $this->attributeRenderer = $this->getLayout()->createBlock(
                    Attributes::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                );
            }
        } catch (LocalizedException $localizedException) {
            $this->attributeRenderer = [];
        }
        return $this->attributeRenderer;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface|Renderer\Conditions
     */
    public function getConditionRenderer()
    {
        try {
            if (!$this->conditionRenderer) {
                $this->conditionRenderer = $this->getLayout()->createBlock(
                    Conditions::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                );
            }
        } catch (LocalizedException $localizedException) {
            $this->conditionRenderer = [];
        }
        return $this->conditionRenderer;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface|Renderer\ProductTypes
     */
    public function getProductTypeRenderer()
    {
        try {
            if (!$this->productTypeRenderer) {
                $this->productTypeRenderer = $this->getLayout()->createBlock(
                    ProductTypes::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                );
            }
        } catch (LocalizedException $localizedException) {
            $this->productTypeRenderer = [];
        }
        return $this->productTypeRenderer;
    }

    /**
     * @param DataObject $row
     */
    public function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $attribute = $row->getData('attribute');
        if ($attribute) {
            $options['option_' . $this->getAttributeRenderer()->calcOptionHash($attribute)] = 'selected="selected"';
        }
        $condition = $row->getData('condition');
        if ($condition) {
            $options['option_' . $this->getConditionRenderer()->calcOptionHash($condition)] = 'selected="selected"';
        }
        $productType = $row->getData('product_type');
        if ($condition) {
            $options['option_' . $this->getProductTypeRenderer()->calcOptionHash($productType)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}
