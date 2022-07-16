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

use Mirasvit\ReportApi\Api\RequestInterface;

class Geo extends Overview
{

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Sales by Geo-data');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->setPrimaryDimensions([
            'sales_order_address|country',
            'mst_reports_postcode|state',
            'mst_reports_postcode|place',
            'mst_reports_postcode|postcode',
        ])->setDimensions([
            'sales_order_address|country',
        ]);

        //        $dimension = $this->context->getRequest()->getParam('dimension');

        //        switch ($dimension) {
        //            case 'mst_reports_postcode|state':
        //                $this->addInternalColumns(['sales_order_address|country']);
        //                break;
        //            case 'mst_reports_postcode|place':
        //                $this->addInternalColumns([
        //                    'sales_order_address|country',
        //                    'mst_reports_postcode|state',
        //                    'mst_reports_postcode|lat',
        //                    'mst_reports_postcode|lng',
        //                ]);
        //                break;
        //            case 'mst_reports_postcode|postcode':
        //                $this->addInternalColumns([
        //                    'sales_order_address|country',
        //                    'mst_reports_postcode|state',
        //                    'mst_reports_postcode|place',
        //                    'mst_reports_postcode|lat',
        //                    'mst_reports_postcode|lng',
        //                ]);
        //                break;
        //        }

        $this->getGridConfig()->disablePagination();

        $this->getChartConfig()
            ->setType('geo')
            ->setDefaultColumns([
                'sales_order|grand_total__sum',
            ]);

        return $this;
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function modifyRequest(RequestInterface $request)
    {
        if (in_array('mst_reports_postcode|place', $request->getDimensions())
            || in_array('mst_reports_postcode|postcode', $request->getDimensions())) {
            $request->addColumn('mst_reports_postcode|lat');
            $request->addColumn('mst_reports_postcode|lng');
            $request->addColumn('mst_reports_postcode|place');
        }

        return $request;
    }
}
