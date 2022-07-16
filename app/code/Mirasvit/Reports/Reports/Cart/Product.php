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



namespace Mirasvit\Reports\Reports\Cart;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Context;
use Mirasvit\Report\Model\Query\Select;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Processor\ResponseItem;
use Mirasvit\Reports\Model\ConfigProvider;

class Product extends AbstractReport
{
    /**
     * @var ConfigProvider
     */
    protected $config;

    /**
     * Product constructor.
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
        return __('Abandoned Products');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTable('catalog_product_entity');

        $this->setPrimaryFilters([
            'quote|created_at',
            'quote|store_id',
        ]);

        $this->setInternalColumns(
            ['catalog_product_entity|entity_id']
        )->setColumns([
            'catalog_product_entity|sku',
            'catalog_product_entity|name',
            'quote|entity_id__cnt',
            'quote_item|qty__sum',
        ]);

        //        $this->addAvailableFilters($this->context->getProvider()->getSimpleColumns('quote'));

        //        $map = $this->context->getProvider();
        $this->setPrimaryDimensions([
            'catalog_product_entity|sku',
        ])->setDimensions([
            'catalog_product_entity|sku',
        ]);

        //        $this->addColumns($map->getSimpleColumns('catalog_product_entity'))
        //            ->addColumns($map->getSimpleColumns('cataloginventory_stock_item'))
        //            ->addColumns($map->getComplexColumns('quote_item'));

        $this->setInternalFilters([
            [
                'column'        => 'quote|is_active',
                'value'         => '1',
                'conditionType' => 'eq',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions(ResponseItem $item, RequestInterface $request)
    {
        return false;
        $id          = $item['catalog_product_entity|entity_id'];
        $quoteParams = ['catalog_product_entity|entity_id' => [
            'from' => $id,
            'to'   => $id,
        ]];

        $dateFilter = $this->getUiContext()->context->getFilterParam('quote|created_at');
        if (is_array($dateFilter) && isset($dateFilter['from'], $dateFilter['to'])) {
            // key corresponds to the fast filter of the 'cart_product_detail' report
            $quoteParams['quote|created_at__day'] = [
                'from' => $dateFilter['from'],
                'to'   => $dateFilter['to'],
            ];
        }

        return [
            [
                'label' => __('View Product'),
                'href'  => $this->context->urlManager->getUrl('catalog/product/edit', ['id' => $id]),
            ],
            [
                'label' => __('View Quotes'),
                'href'  => $this->getReportUrl('cart_product_detail', $quoteParams),
            ],
        ];
    }
}
