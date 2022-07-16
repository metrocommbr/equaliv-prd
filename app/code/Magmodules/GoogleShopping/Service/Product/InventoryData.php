<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Service\Product;

use Magento\Framework\App\ResourceConnection;

/**
 * Class InventoryData
 *
 * @package Magmodules\GoogleShopping\Service\Product
 */
class InventoryData
{

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * InventoryData constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $config
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function addDataToProduct($product, $config)
    {
        if (empty($config['inventory']['stock_id'])) {
            return $product;
        }

        if ($product->getData('use_config_manage_stock') == 1 && $config['inventory']['config_manage_stock'] == 0) {
            return $product->setIsSalable(1)->setIsInStock(1);
        }

        /**
         * Return if product is not of simple type
         */
        if ($product->getTypeId() != 'simple') {
            return $product;
        }

        $inventoryData = $this->getInventoryData($product->getSku(), $config['inventory']['stock_id']);
        $isSalable = isset($inventoryData['is_salable']) ? $inventoryData['is_salable'] : $product->getIsInStock();

        return $product->setIsSalable($isSalable)->setIsInStock($isSalable);
    }

    /**
     * @param $sku
     * @param $stockId
     *
     * @return array|void
     */
    private function getInventoryData($sku, $stockId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_stock_' . $stockId);

        if (!$connection->isTableExists($tableName)) {
            return;
        }

        $select = $connection->select()
            ->from($tableName)
            ->where('sku = ?', $sku)
            ->limit(1);

        if ($stockData = $connection->fetchRow($select)) {
            return $stockData;
        }
    }
}
