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
 * @package   mirasvit/module-report
 * @version   1.4.3
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Setup\Patch\Schema;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddSalesTableIndexes implements SchemaPatchInterface
{

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        SchemaSetupInterface $schemaSetup,
        ResourceConnection $resourceConnection
    ) {
        $this->schemaSetup = $schemaSetup;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        //this script does not overwrite existing data
        $this->schemaSetup->getConnection()->startSetup();
        $setup = $this->schemaSetup;
        $keys = [
            'sales_order'      => [
                'created_at',
            ],
            'sales_order_item' => [
                'product_id',
            ],
        ];

        foreach ($keys as $table => $columns) {
            foreach ($columns as $column) {
                if(!$setup->getConnection()->isTableExists($setup->getTable($table))) {
                    continue;
                }

                $indexes  = $setup->getConnection()->getIndexList($setup->getTable($table));
                $isExists = false;

                foreach ($indexes as $index) {
                    if (is_array($index['COLUMNS_LIST']) && in_array($column, $index['COLUMNS_LIST'])) {
                        $isExists = true;
                    }
                }
                if ($isExists) {
                    continue;
                }

                $setup->getConnection()->addIndex(
                    $setup->getTable($table),
                    $setup->getConnection()->getIndexName(
                        $setup->getTable($table),
                        [$column]
                    ),
                    [$column]
                );
            }
        }
        $this->schemaSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }
}
