<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Block\Adminhtml\Magmodules;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Magento\Backend\Block\Template\Context;

/**
 * Class Support
 *
 * @package Magmodules\GoogleShopping\Block\Adminhtml\Magmodules
 */
class Support extends Field
{

    const MODULE_CODE = 'googleshopping-magento2';
    const SUPPORT_LINK = 'https://www.magmodules.eu/help/' . self::MODULE_CODE;
    const MANUAL_LINK = 'https://www.magmodules.eu/help/googleshopping-magento2/configure-google-shopping-magento2';

    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * Version constructor.
     *
     * @param Context       $context
     * @param GeneralHelper $generalHelper
     */
    public function __construct(
        Context $context,
        GeneralHelper $generalHelper
    ) {
        $this->generalHelper = $generalHelper;
        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $html = sprintf(
            '<a href="%s" class="support-link">%s</a> &nbsp; | &nbsp; <a href="%s" class="support-link">%s</a>',
            self::MANUAL_LINK,
            __('Online Manual'),
            self::SUPPORT_LINK,
            __('FAQ')
        );

        $element->setData('text', $html);
        return parent::_getElementHtml($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _renderScopeLabel(AbstractElement $element)
    {
        return '';
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _renderInheritCheckbox(AbstractElement $element)
    {
        return '';
    }
}
