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
use Mirasvit\Report\Model\Query\Select;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Processor\ResponseItem;

class Abandoned extends AbstractReport
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Abandoned Carts');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTable('quote');

        $this->setPrimaryFilters([
            'quote|created_at',
            'quote|store_id',
        ]);

        $this->setInternalColumns([
            'quote|entity_id',
            'quote|customer_id',
            'quote|is_active',
        ]);

        $this->setColumns([
            'quote|customer_name',
            'quote|created_at',
            'quote|items_qty',
            'quote|products',
        ]);


        $this->setPrimaryDimensions([
            'quote|entity_id',
        ])->setDimensions([
            'quote|entity_id',
        ]);

        $this->setInternalFilters([
            [
                'column'        => 'quote|is_active',
                'conditionType' => 'eq',
                'value'         => '1',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions(ResponseItem $item, RequestInterface $request)
    {
        if ($item->getData('quote|customer_id') && $item->getData('quote|customer_id')) {
            return [
                [
                    'label' => __('View Customer'),
                    'href'  => $this->context->urlManager->getUrl(
                        'customer/index/edit',
                        ['id' => $item->getData('quote|customer_id')]
                    ),
                ],
            ];
        }

    }
}