<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\DataObject;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field\Renderer\Countries;

/**
 * Class ShippingPrices
 *
 * @package Magmodules\GoogleShopping\Block\Adminhtml\System\Config\Form\Field
 */
class ShippingPrices extends AbstractFieldArray
{

    /**
     * @var Countries
     */
    private $countryRenderer;

    /**
     * Render block
     */
    public function _prepareToRender()
    {
        $this->addColumn('code', [
            'label'    => __('Country'),
            'renderer' => $this->getCountryRenderer()
        ]);
        $this->addColumn('service', [
            'label' => __('Service'),
        ]);
        $this->addColumn('price_from', [
            'label' => __('From Price'),
        ]);
        $this->addColumn('price_to', [
            'label' => __('To Price'),
        ]);
        $this->addColumn('price', [
            'label' => __('Shipping Costs'),
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface|Renderer\Countries
     */
    public function getCountryRenderer()
    {
        try {
            if (!$this->countryRenderer) {
                $this->countryRenderer = $this->getLayout()->createBlock(
                    Countries::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                );
            }
        } catch (LocalizedException $localizedException) {
            $this->countryRenderer = [];
        }

        return $this->countryRenderer;
    }

    /**
     * @param DataObject $row
     */
    public function _prepareArrayRow(DataObject $row)
    {
        $attribute = $row->getData('code');
        $options = [];
        if ($attribute) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($attribute)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}
