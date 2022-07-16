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



namespace Mirasvit\Reports\Service\Pills;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\ReportApi\Api\Config\ColumnInterface;
use Mirasvit\ReportApi\Api\Config\TableInterface;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Api\Service\SelectPillInterface;
use Mirasvit\ReportApi\Handler\Select;

/**
 * Class ChildItemPill
 * Add parent item's sales values to child items.
 * Because child items (simple products) do not have such values.
 * These values are stored on a parent item level (configurable products).
 */
class ChildItemPill implements SelectPillInterface
{
    private $attributeRepository;

    private $resource;

    public function __construct(
        ResourceConnection $resource,
        ProductAttributeRepositoryInterface $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->resource            = $resource;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @inheritdoc
     */
    public function isApplicable(RequestInterface $request, ColumnInterface $column, TableInterface $table)
    {
        if ($column->getTable()->getName() === 'sales_order_item') {
            return true;
        }

        return false;
    }

    /**
     * Temporary fix to retrieve correct sales values from the parent
     * item instead of a child (it does not have sales values).
     * Fix applied only if the dimension used for report is the attribute used for configurable products.
     * {@inheritdoc}
     */
    public function take(
        Select $select,
        ColumnInterface $column,
        TableInterface $baseTable,
        RequestInterface $request
    ) {
        $attributeCode = $column->getFields()[0]->getName();

        if (!$this->isAttributeApplicable($attributeCode)) {
            return;
        }

        $alias = 'sales_order_item_parent' . rand(0, 1000);

        #remove old column
        $columns = $select->getPart('columns');
        foreach ($columns as $idx => $item) {
            if ($item[2] === $column->getName()) {
                unset($columns[$idx]);
            }
        }
        $select->setPart('columns', $columns);

        $select->columns([
            $column->getName() => new \Zend_Db_Expr(
                'IF(' . $column->toDbExpr() . ' = 0, '
                . str_replace($column->getTable()->getName(), $alias, $column->toDbExpr())
                . ', ' . $column->toDbExpr()
                . ')'
            ),
        ]);

        $select->joinLeft(
            [$alias => $this->resource->getTableName($column->getTable()->getName())],
            $column->getTable()->getName() . '.parent_item_id = ' . $alias . '.item_id',
            ''
        );
    }

    /**
     * @param string $attributeCode
     *
     * @return bool
     */
    public function isAttributeApplicable($attributeCode)
    {
        if (in_array($attributeCode, ['qty_ordered', 'base_amount_refunded', 'base_row_total'])) {
            return true;
        }

        return false;
    }
}
