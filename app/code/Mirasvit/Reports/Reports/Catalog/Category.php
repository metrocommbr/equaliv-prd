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



namespace Mirasvit\Reports\Reports\Catalog;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Context;
use Mirasvit\Report\Model\Query\Select;
use Mirasvit\Reports\Model\ConfigProvider;

class Category extends AbstractReport
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    public function __construct(
        ConfigProvider $config,
        Context $context
    ) {
        $this->configProvider = $config;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Sales by Category');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTable('catalog_category_entity');

        $this->setPrimaryFilters([
            'sales_order|created_at',
            'sales_order|store_id',
            'sales_order|status',
        ]);

        $this->setInternalColumns([
            'catalog_category_entity|entity_id',
        ]);

        $this->setColumns([
            'catalog_category_entity|name',
            'sales_order|entity_id__cnt',
            'sales_order_item|qty_ordered__sum',
            'sales_order_item|tax_amount__sum',
            'sales_order_item|discount_amount__sum',
            'sales_order_item|amount_refunded__sum',
            'sales_order_item|row_total__sum',
        ]);

        $this->setPrimaryDimensions([
            'catalog_category_entity|entity_id',
        ])->setDimensions([
            'catalog_category_entity|entity_id',
        ]);

        $this->setFilters([
            [
                'column'        => 'sales_order|status',
                'conditionType' => 'in',
                'value'         => $this->configProvider->getFilterableOrderStatuses(),
            ],
        ]);
    }
}
