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



namespace Mirasvit\Reports\Reports\Order;


class CustomerGroup extends Overview
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Sales by Customer Group');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->setDimensions([
            'sales_order|customer_group_id',
        ])->setPrimaryDimensions([
            'sales_order|customer_group_id',
        ]);

        $this->getChartConfig()->setType('pie');

        return $this;
    }
}