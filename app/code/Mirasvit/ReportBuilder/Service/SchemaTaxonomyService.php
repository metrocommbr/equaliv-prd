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
 * @package   mirasvit/module-report-builder
 * @version   1.1.2
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportBuilder\Service;

use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\ColumnInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;

class SchemaTaxonomyService
{
    const GROUP = 'group';
    const TABLE = 'table';
    const FIELD = 'field';
    const AGGREGATOR = 'aggregator';
    const COLUMN = 'column';

    /**
     * @var SchemaInterface
     */
    private $schema;

    /**
     * SchemaTaxonomyService constructor.
     * @param SchemaInterface $schema
     */
    public function __construct(
        SchemaInterface $schema
    ) {
        $this->schema = $schema;
    }

    /**
     * @return array
     */
    public function build()
    {
        $taxonomy = [
            self::GROUP      => [],
            self::TABLE      => [],
            self::FIELD      => [],
            self::AGGREGATOR => [],
            self::COLUMN     => [],
        ];

        foreach ($this->schema->getTables() as $table) {
            $groupName = strtolower($table->getGroup() ? $table->getGroup() : 'other');

            $taxonomy[self::GROUP][$groupName] = [
                'identifier' => $groupName,
                'label'      => $table->getGroup() ? $table->getGroup() : 'Other',
            ];

            $tableName = $table->getName();

            $taxonomy[self::TABLE][$tableName] = [
                'identifier' => $tableName,
                'label'      => $table->getLabel(),
                self::GROUP  => [$groupName],
            ];

            foreach ($table->getColumns() as $column) {
                if (!$column->getLabel()) {
                    continue;
                }

                list($fieldName,) = explode('__', $column);

                if (!isset($taxonomy[self::FIELD][$fieldName])) {
                    $taxonomy[self::FIELD][$fieldName] = [
                        'identifier' => $fieldName,
                        'label'      => (string)$column->getLabel(),
                        'type'       => $column->getType()->getType(),
                        'isInternal' => $column->isInternal(),
                        self::TABLE  => [$tableName],
                    ];
                }

                $aggregatorName = $column->getAggregator()->getType();

                if (!isset($taxonomy[self::AGGREGATOR][$aggregatorName])) {
                    $taxonomy[self::AGGREGATOR][$aggregatorName] = [
                        'identifier' => $aggregatorName,
                        'label'      => $column->getAggregator()->getLabel(),
                        self::FIELD  => [],
                    ];
                }
                $taxonomy[self::AGGREGATOR][$aggregatorName][self::FIELD][] = $fieldName;

                $taxonomy[self::COLUMN][] = [
                    'identifier'     => $column->getIdentifier(),
                    self::GROUP      => $groupName,
                    self::TABLE      => $tableName,
                    self::FIELD      => $fieldName,
                    self::AGGREGATOR => $aggregatorName,
                    'isInternal'     => $column->isInternal(),
                    'isDimension'    => $this->isDimension($column),
                    'isChart'        => $this->isChart($column),
                    'isFastFilter'   => $this->isFastFilter($column),
                ];
            }
        }

        $taxonomy[self::GROUP] = array_values($taxonomy[self::GROUP]);
        $taxonomy[self::TABLE] = array_values($taxonomy[self::TABLE]);
        $taxonomy[self::FIELD] = array_values($taxonomy[self::FIELD]);
        $taxonomy[self::AGGREGATOR] = array_values($taxonomy[self::AGGREGATOR]);
        $taxonomy[self::COLUMN] = array_values($taxonomy[self::COLUMN]);

        return $taxonomy;
    }

    /**
     * @param ColumnInterface $column
     * @return bool
     */
    private function isDimension(ColumnInterface $column)
    {
        $type = $column->getType()->getType();
        $agg = $column->getAggregator()->getType();

        switch ($type) {
            case TypeInterface::TYPE_DATE:
            case TypeInterface::TYPE_STORE:
                return true;
            case TypeInterface::TYPE_SELECT:
            case TypeInterface::TYPE_STR:
            case TypeInterface::TYPE_FK:
                return in_array($agg, [AggregatorInterface::TYPE_NONE], true);
        }

        return false;
    }

    /**
     * @param ColumnInterface $column
     * @return bool
     */
    private function isChart(ColumnInterface $column)
    {
        $type = $column->getType()->getType();

        switch ($type) {
            case TypeInterface::TYPE_MONEY:
            case TypeInterface::TYPE_NUMBER:
                return true;
        }

        return false;
    }

    /**
     * @param ColumnInterface $column
     * @return bool
     */
    private function isFastFilter(ColumnInterface $column)
    {
        $type = $column->getType()->getType();
        $agg = $column->getAggregator()->getType();

        if ($agg !== AggregatorInterface::TYPE_NONE) {
            return false;
        }

        switch ($type) {
            case TypeInterface::TYPE_DATE:
            case TypeInterface::TYPE_STORE:
            case TypeInterface::TYPE_SELECT:
                return true;
        }

        return false;
    }
}
