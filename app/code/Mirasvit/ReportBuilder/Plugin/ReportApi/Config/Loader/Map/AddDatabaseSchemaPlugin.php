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



namespace Mirasvit\ReportBuilder\Plugin\ReportApi\Config\Loader\Map;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\ReportApi\Api\Config\TableInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;
use Mirasvit\ReportApi\Api\Service\TableServiceInterface;
use Mirasvit\ReportApi\Config\Entity\Column;
use Mirasvit\ReportApi\Config\Entity\Relation;
use Mirasvit\ReportApi\Config\Entity\Table;
use Mirasvit\ReportApi\Config\Loader\Map;

class AddDatabaseSchemaPlugin
{
    /**
     * @var TableServiceInterface
     */
    private $tableService;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var SchemaInterface
     */
    private $schema;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * AddDatabaseSchemaPlugin constructor.
     * @param TableServiceInterface $tableService
     * @param SchemaInterface $schema
     * @param ObjectManagerInterface $objectManager
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        TableServiceInterface $tableService,
        SchemaInterface $schema,
        ObjectManagerInterface $objectManager,
        DeploymentConfig $deploymentConfig
    ) {
        $this->tableService     = $tableService;
        $this->schema           = $schema;
        $this->objectManager    = $objectManager;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @param Map $map
     * @param mixed $result
     * @return mixed
     */
    public function afterLoad(Map $map, $result)
    {
        // add tables
        foreach ($this->tableService->getTables() as $tableName) {
            $tableName = $this->getTableName($tableName);

            if (!$this->isAllowedTable($tableName)) {
                continue;
            }

            if (!$this->schema->hasTable($tableName)) {
                /** @var Table $table */
                $table = $this->objectManager->create(Table::class, [
                    'name'  => $tableName,
                    'label' => false,
                ]);
                $this->schema->addTable($table);
            }

            $table = $this->schema->getTable($tableName);
            $this->initColumns($table);
        }

        foreach ($this->schema->getTables() as $table) {
            $foreignKeys = $this->tableService->getForeignKeys($table);
            foreach ($foreignKeys as $fk) {
                $leftTable  = $this->getTableName($fk['TABLE_NAME']);
                $rightTable = $this->getTableName($fk['REF_TABLE_NAME']);

                if (!$this->schema->hasTable($leftTable)
                    || !$this->schema->hasTable($rightTable)) {
                    continue;
                }

                $relations[] = [
                    'leftTable'  => $leftTable,
                    'leftField'  => $fk['COLUMN_NAME'],
                    'rightTable' => $rightTable,
                    'rightField' => $fk['REF_COLUMN_NAME'],
                    'type'       => '1n',
                ];
            }
        }

        foreach ($relations as $item) {
            $leftTable  = $this->schema->getTable($item['leftTable']);
            $rightTable = $this->schema->getTable($item['rightTable']);
            $data       = [
                'leftTable'  => $leftTable,
                'leftField'  => $leftTable->getField($item['leftField']),
                'rightTable' => $rightTable,
                'rightField' => $rightTable->getField($item['rightField']),
                'type'       => $item['type'],
            ];

            $relation = $this->objectManager->create(Relation::class, $data);

            $this->schema->addRelation($relation);
        }

        return $result;
    }

    /**
     * @param TableInterface $table
     */
    private function initColumns(TableInterface $table)
    {
        // add columns
        $description = $this->tableService->describeTable($table);
        $foreignKeys = $this->tableService->getForeignKeys($table);

        foreach ($description as $info) {
            if ($info['IDENTITY']) {
                $type = 'pk';
            } else {
                $isFk = false;
                foreach ($foreignKeys as $key) {
                    if ($key['COLUMN_NAME'] == $info['COLUMN_NAME']) {
                        $isFk = true;
                    }
                }

                if ($isFk) {
                    $type = 'fk';
                } else {
                    if ($info['DATA_TYPE'] == 'varchar'
                        || $info['DATA_TYPE'] == 'tinytext'
                    ) {
                        $type = 'string';
                    } elseif ($info['DATA_TYPE'] == 'int'
                        || $info['DATA_TYPE'] == 'smallint'
                        || $info['DATA_TYPE'] == 'float'
                        || $info['DATA_TYPE'] == 'bigint'
                        || $info['DATA_TYPE'] == 'tinyint'
                        || $info['DATA_TYPE'] == 'double') {
                        $type = 'number';
                    } elseif ($info['DATA_TYPE'] == 'timestamp'
                        || $info['DATA_TYPE'] == 'datetime'
                        || $info['DATA_TYPE'] == 'date') {
                        $type = 'date';
                    } elseif ($info['DATA_TYPE'] == 'text'
                        || $info['DATA_TYPE'] == 'mediumblob'
                        || $info['DATA_TYPE'] == 'mediumtext'
                        || $info['DATA_TYPE'] == 'longtext'
                        || $info['DATA_TYPE'] == 'longblob'
                        || $info['DATA_TYPE'] == 'blob') {
                        $type = 'html';
                    } elseif ($info['DATA_TYPE'] == 'decimal') {
                        $type = 'money';
                    } else {
                        $type = 'string';
                    }
                }
            }

            $type = $this->objectManager->get($this->schema->getType($type));

            foreach ($type->getAggregators() as $aggregatorName) {
                $aggregator = $this->schema->getAggregator($aggregatorName);
                $aggregator = $this->objectManager->get($aggregator);

                $name = $info['COLUMN_NAME'];

                $columnName = $name . ($aggregatorName !== 'none' ? "__$aggregatorName" : '');

                $data = [
                    'name'       => $columnName,
                    'type'       => $type,
                    'aggregator' => $aggregator,
                    'data'       => [
                        'label'    => $info['COLUMN_NAME'],
                        'internal' => true,
                        'table'    => $table,
                        'fields'   => [$name],
                    ],
                ];

                if (!isset($table->getColumns()[$columnName])) {
                    $this->objectManager->create(Column::class, $data);
                }
            }
        }
    }

    /**
     * @param string $tableName
     * @return bool
     */
    private function isAllowedTable($tableName)
    {
        $exceptions = [
            '/mageworx/',
            '/^admin_/',
            '/^adminnotification_/',
            '/^authorization_/',
            '/^cache/',
            '/^captcha_/',
            '/_datetime$/',
            '/_decimal$/',
            '/_int$/',
            '/_text$/',
            '/_varchar$/',
            '/_gallery$/',
            '/_media_gallery_value/',
            '/rewrite/',
            '/session/',
            '/^flag$/',
            '/_index/',
            '/_idx/',
            '/_tmp/',
            '/tmp_/',
            '/_replica/',
            '/_scope[0-9]*/',
            '/^sequence_/',
            '/aggregated/',
            '/alert/',
            '/^eav_/',
            '/^reporting_/',
            '/_eav/',
            '/catalog_product_option/',
            '/downloadable_link/',
            '/catalog_product_bundle/',
            '/^paypal_/',
            '/^vault_/',
            '/^temando_/',
            '/^weee_/',
            '/^oauth_/',
            '/^password_/',
            '/^persistent_/',
            '/tmp/',
            '/^widget/',
            '/^vertex/',
            '/^ui_bookmark/',
            '/^translation$/',
            '/^theme/',
            '/setup_module/',
            '/_log$/',
            '/^amasty_/',
            '/^aw_/',
            '/^itoris_/',
            '/^yotpo_/',
            '/^channel_/',
            '/mp_bettersort_/',
        ];

        foreach ($exceptions as $pattern) {
            if (preg_match($pattern, $tableName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $tableName
     * @return string
     */
    private function getTableName($tableName)
    {
        $tablePrefix = $this->deploymentConfig->get('db/table_prefix');

        //remove table prefix
        if ($tablePrefix && strpos($tableName, $tablePrefix) === 0) {
            $tableName = substr($tableName, strlen($tablePrefix));
        }

        return $tableName;
    }
}
