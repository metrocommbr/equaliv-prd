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

use Magento\Framework\Stdlib\DateTime;
use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Context;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Processor\ResponseItem;
use Mirasvit\Reports\Model\ConfigProvider;

class Overview extends AbstractReport
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

    public function getName()
    {
        return __('Sales Overview');
    }

    public function init()
    {
        $this->setTable('sales_order');

        $this->setPrimaryFilters([
            'sales_order|created_at',
            'sales_order|store_id',
            'sales_order|status',
            'sales_order|customer_group_id',
        ]);

        $this->setColumns([
            'sales_order|entity_id__cnt',
            'sales_order|total_qty_ordered__sum',
            'sales_order|discount_amount__sum',
            'sales_order|shipping_amount__sum',
            'sales_order|tax_amount__sum',
            'sales_order|total_refunded__sum',
            'sales_order|gross_margin__avg',
            'sales_order|grand_total__sum',
        ]);

        $this->setDimensions([
            'sales_order|created_at__day',
        ]);

        $this->setPrimaryDimensions([
            'sales_order|created_at__day',
            'sales_order|created_at__week',
            'sales_order|created_at__month',
            'sales_order|created_at__quarter',
            'sales_order|created_at__year',
        ]);

        $this->getChartConfig()
            ->setType('column')
            ->setDefaultColumns([
                'sales_order|grand_total__sum',
            ]);

        $this->setFilters([
            [
                'column'        => 'sales_order|status',
                'conditionType' => 'in',
                'value'         => $this->configProvider->getFilterableOrderStatuses(),
            ],
        ]);
    }


    public function getActions(ResponseItem $item, RequestInterface $request)
    {
        return false;
    }

    /**
     * @param string $dimension
     * @param string $fromDate
     * @param string $report
     *
     * @return bool|string
     * @throws \Zend_Date_Exception
     */
    protected function getRangeUrl($dimension, $fromDate, $report = 'Order_Plain')
    {
        $fromDate = (new DateTime())->strToTime($fromDate);
        $toDate   = $fromDate;

        switch ($dimension) {
            case 'sales_order|created_at__day':
                $toDate += 24 * 60 * 60;
                break;

            case 'sales_order|created_at__week':
                $toDate += 7 * 24 * 60 * 60;
                break;

            case 'sales_order|created_at__month':
                $toDate += 30 * 24 * 60 * 60;
                break;

            case 'sales_order|created_at__year':
                $toDate += 365 * 24 * 60 * 60;
                break;

            default:
                return false;
        }

        return $this->context->urlManager->getUrl(
            'reports/report/view',
            [
                'report' => $report,
                '_query' => [
                    'filters' => [
                        'sales_order|created_at' => [
                            'from' => (new \Zend_Date($fromDate))->get(DateTime::DATETIME_INTERNAL_FORMAT),
                            'to'   => (new \Zend_Date($toDate))->get(DateTime::DATETIME_INTERNAL_FORMAT),
                        ],
                    ],
                ],
            ]
        );
    }
}
