<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-reports
 * @version   1.4.4
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Reports\Block\Adminhtml\Geo;

class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'geo';
        $this->_blockGroup = 'Mirasvit_Reports';
        $this->_mode = 'import';
        $this->_controller = 'adminhtml_geo';

        //        $this->buttonList->add('save', [
        //            'label'   => __('Import Geo Data'),
        //            'onclick' => 'editForm.submit();',
        //            'class'   => 'save',
        //        ], 1);

        return $this;
    }
}
