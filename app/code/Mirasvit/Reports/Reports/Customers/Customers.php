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



namespace Mirasvit\Reports\Reports\Customers;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Context;
use Mirasvit\Reports\Model\ConfigProvider;

class Customers extends AbstractReport
{
    /**
     * @var ConfigProvider
     */
    protected $config;

    /**
     * Customers constructor.
     *
     * @param ConfigProvider $config
     * @param Context        $context
     */
    public function __construct(
        ConfigProvider $config,
        Context $context
    ) {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Customers');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTable('customer_entity');

        //                $this->addFastFilters([
        //                    'customer_entity|entity_id',
        //                ]);

        $this->setColumns([
            'customer_entity|email',
            'customer_entity|firstname',
            'customer_entity|lastname',
            'customer_entity|created_at',
            'customer_entity|group_id',
            'sales_order|entity_id__cnt',
            'sales_order|products',
        ]);

        //it is used in filter by Products column
        $this->setInternalColumns([
            'customer_entity|entity_id',
            //            'sales_order|orders_products',
        ]);

        $this->setDimensions([
            'customer_entity|entity_id',
        ])->setPrimaryDimensions([
            'customer_entity|entity_id',
        ]);
    }
}
