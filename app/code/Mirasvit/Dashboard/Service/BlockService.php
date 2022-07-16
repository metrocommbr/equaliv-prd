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
 * @package   mirasvit/module-dashboard
 * @version   1.3.2
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Dashboard\Service;

use Magento\Framework\Stdlib\ArrayManager;
use Mirasvit\Dashboard\Api\Data\BlockInterface;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Mirasvit\ReportApi\Api\RequestBuilderInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;

class BlockService
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * @var DateServiceInterface
     */
    private $ds;

    /**
     * @var SchemaInterface
     */
    private $schema;

    /**
     * BlockService constructor.
     * @param ArrayManager $arrayManager
     * @param RequestBuilderInterface $requestBuilder
     * @param DateServiceInterface $dateService
     * @param SchemaInterface $schema
     */
    public function __construct(
        ArrayManager $arrayManager,
        RequestBuilderInterface $requestBuilder,
        DateServiceInterface $dateService,
        SchemaInterface $schema
    ) {
        $this->arrayManager   = $arrayManager;
        $this->requestBuilder = $requestBuilder;
        $this->ds             = $dateService;
        $this->schema         = $schema;
    }

    /**
     * @param BlockInterface $block
     * @param array $filters
     * @return \Mirasvit\ReportApi\Api\ResponseInterface|null
     */
    public function getApiResponse(BlockInterface $block, array $filters)
    {
        $renderer = $block->getConfig()->getRenderer();

        $filters = array_merge($filters, $block->getConfig()->getFilters());

        $rTable      = null;
        $rColumns    = [];
        $rDimensions = [];
        $rFilters    = [];
        $dateColumn  = null;
        $rPageSize   = 1000;

        $request = $this->requestBuilder->create();

        if ($renderer === 'table') {
            $dimensions = $block->getConfig()->getTable()->getDimensions();

            if (count($dimensions) == 0) {
                return null;
            }

            $rDimensions = $dimensions;

            $dimensionColumn = $this->schema->getColumn($dimensions[0]);

            if (!$dimensionColumn) {
                return null;
            }

            $rTable = $dimensionColumn->getTable()->getName();

            $rColumns = array_merge($rDimensions, $block->getConfig()->getTable()->getColumns());

            $rPageSize = $block->getConfig()->getTable()->getPageSize();
            $orders    = $block->getConfig()->getTable()->getSortOrders();
            foreach ($orders as $item) {
                $request->addSortOrder($item['column'], $item['direction']);
            }

            $dateColumn = $this->getDateColumn($rTable, $filters);
        } elseif ($renderer === 'chart') {
            $dimension = $block->getConfig()->getChart()->getDimension();

            $rDimensions = [$dimension];

            $dimensionColumn = $this->schema->getColumn($dimension);

            if (!$dimensionColumn) {
                return null;
            }

            $rTable = $dimensionColumn->getTable()->getName();

            $rColumns = array_merge($rDimensions, $block->getConfig()->getChart()->getColumns());

            $dateColumn = $this->getDateColumn($rTable, $filters);
        } else {
            $identifier = $block->getConfig()->getSingle()->getColumn();
            $column     = $this->schema->getColumn($identifier);

            if (!$column) {
                return null;
            }

            $rTable = $column->getTable()->getName();

            $rColumns[] = $column->getIdentifier();

            $dateColumn = $this->getDateColumn($rTable, $filters);

            if ($dateColumn) {
                $dimensionColumn = $this->schema->getColumn("$rTable|created_at__day");

                if ($dimensionColumn) {
                    $rColumns[]    = $dimensionColumn->getIdentifier();
                    $rDimensions[] = $dimensionColumn->getIdentifier();
                }
            }
        }

        if ($dateColumn) {
            $from = null;
            $to   = null;
            foreach ($filters as $filter) {
                if ($filter['column'] === 'DATE') {
                    $filter['column'] = $dateColumn->getIdentifier();

                    $filter['group'] = 'A';

                    if ($block->getConfig()->getDateRange()->isOverride()) {
                        $range = $this->ds->getInterval(
                            $block->getConfig()->getDateRange()->getRange()
                        );

                        if ($filter['condition_type'] == 'gteq') {
                            $filter['value'] = $this->ds->toMysqlDate($range->getFrom());
                        }

                        if ($filter['condition_type'] == 'lteq') {
                            $filter['value'] = $this->ds->toMysqlDate($range->getTo());
                        }
                    }

                    if ($filter['condition_type'] == 'gteq') {
                        $from = $filter['value'];
                    }

                    if ($filter['condition_type'] == 'lteq') {
                        $to = $filter['value'];
                    }
                }

                $rFilters[] = $filter;
            }

            if ($block->getConfig()->getSingle()->getCompare()) {
                $previous = $this->ds->getPreviousInterval(
                    $this->ds->toInterval($from, $to),
                    $block->getConfig()->getSingle()->getCompare()
                );

                $rFilters[] = [
                    'column'         => $dateColumn->getIdentifier(),
                    'condition_type' => 'gteq',
                    'value'          => $this->ds->toMysqlDate($previous->getFrom()),
                    'group'          => 'C',
                ];

                $rFilters[] = [
                    'column'         => $dateColumn->getIdentifier(),
                    'condition_type' => 'lteq',
                    'value'          => $this->ds->toMysqlDate($previous->getTo()),
                    'group'          => 'C',
                ];
            }

            if ($block->getConfig()->getChart()->getCompare()) {
                $previous = $this->ds->getPreviousInterval(
                    $this->ds->toInterval($from, $to),
                    $block->getConfig()->getChart()->getCompare()
                );

                $rFilters[] = [
                    'column'         => $dateColumn->getIdentifier(),
                    'condition_type' => 'gteq',
                    'value'          => $this->ds->toMysqlDate($previous->getFrom()),
                    'group'          => 'C',
                ];

                $rFilters[] = [
                    'column'         => $dateColumn->getIdentifier(),
                    'condition_type' => 'lteq',
                    'value'          => $this->ds->toMysqlDate($previous->getTo()),
                    'group'          => 'C',
                ];
            }
        }

        $request
            ->setTable($this->getTable($block))
            ->setColumns($rColumns)
            ->setDimensions($rDimensions)
            ->setPageSize($rPageSize);

        foreach ($rFilters as $filter) {
            if ($filter['value'] === 'DATE') {
                continue;
            }

            $request->addFilter(
                $filter['column'],
                $filter['value'],
                $filter['condition_type'],
                isset($filter['group']) ? $filter['group'] : ''
            );
        }

        return $request->process();
    }

    /**
     * @param BlockInterface $block
     * @return bool|string
     */
    private function getTable(BlockInterface $block)
    {
        if ($block->getConfig()->getRenderer() === 'single') {
            $column = $this->schema->getColumn(
                $block->getConfig()->getSingle()->getColumn()
            );

            if ($column) {
                return $column->getTable()->getName();
            }
        }

        if ($block->getConfig()->getRenderer() === 'table') {
            $dimensions = $block->getConfig()->getTable()->getDimensions();
            if (count($dimensions) > 0) {
                $column = $this->schema->getColumn($dimensions[0]);

                if ($column) {
                    return $column->getTable()->getName();
                }
            }
        }

        if ($block->getConfig()->getRenderer() === 'chart') {
            $dimension = $block->getConfig()->getChart()->getDimension();
            $column    = $this->schema->getColumn($dimension);

            if ($column) {
                return $column->getTable()->getName();
            }
        }

        return false;
    }

    /**
     * @param string $tableIdentifier
     * @param array  $filters
     *
     * @return \Mirasvit\ReportApi\Api\Config\ColumnInterface|null
     */
    private function getDateColumn($tableIdentifier, $filters)
    {
        try {
            $column = $this->schema->getColumn("$tableIdentifier|created_at");
        } catch (\Exception $e) {
            $column = null;
        }

        foreach ($filters as $filter) {
            if ($filter['value'] === 'DATE') {
                $column = $this->schema->getColumn($filter['column']);
            }
        }

        return $column;
    }
}
