<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Model;

use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;
use Magmodules\GoogleShopping\Model\Collection\Products as ProductModel;
use Magmodules\GoogleShopping\Helper\Source as SourceHelper;
use Magmodules\GoogleShopping\Helper\Product as ProductHelper;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Magmodules\GoogleShopping\Helper\Feed as FeedHelper;
use Magento\Framework\Exception\LocalizedException;
use Magmodules\GoogleShopping\Exceptions\Validation as ValidationException;

/**
 * Class Feed
 *
 * @package Magmodules\GoogleShopping\Model
 */
class Feed
{

    const XPATH_FEED_RESULT = 'magmodules_googleshopping/feeds/results';
    const XPATH_GENERATE = 'magmodules_googleshopping/generate/enable';
    /**
     * @var ProductModel
     */
    private $productModel;
    /**
     * @var SourceHelper
     */
    private $sourceHelper;
    /**
     * @var ProductHelper
     */
    private $productHelper;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;
    /**
     * @var FeedHelper
     */
    private $feedHelper;
    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * Feed constructor.
     *
     * @param ProductModel  $productModel
     * @param SourceHelper  $sourceHelper
     * @param ProductHelper $productHelper
     * @param GeneralHelper $generalHelper
     * @param FeedHelper    $feedHelper
     * @param Emulation     $appEmulation
     */
    public function __construct(
        ProductModel $productModel,
        SourceHelper $sourceHelper,
        ProductHelper $productHelper,
        GeneralHelper $generalHelper,
        FeedHelper $feedHelper,
        Emulation $appEmulation
    ) {
        $this->productModel = $productModel;
        $this->sourceHelper = $sourceHelper;
        $this->productHelper = $productHelper;
        $this->generalHelper = $generalHelper;
        $this->feedHelper = $feedHelper;
        $this->appEmulation = $appEmulation;
    }

    /**
     * Generate all enabled feeds
     */
    public function generateAll()
    {
        $storeIds = $this->generalHelper->getEnabledArray(self::XPATH_GENERATE);
        foreach ($storeIds as $storeId) {
            $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

            try {
                $this->generateByStore($storeId, 'cron');
            } catch (\Exception $e) {
                $this->generalHelper->addTolog('Generate', $e->getMessage());
            }

            $this->appEmulation->stopEnvironmentEmulation();
        }
    }

    /**
     * @param        $storeId
     * @param string $type
     * @param array  $productIds
     * @param int    $page
     * @param bool   $data
     *
     * @return array
     * @throws LocalizedException
     * @throws ValidationException
     */
    public function generateByStore($storeId, $type = 'manual', $productIds = [], $page = 1, $data = false)
    {
        $processed = 0;
        $pages = 1;

        $timeStart = microtime(true);
        $config = $this->sourceHelper->getConfig($storeId, $type);
        $this->feedHelper->createFeed($config);
        $productCollection = $this->productModel->getCollection($config, $page, $productIds);
        $size = $this->productModel->getCollectionCountWithFilters($productCollection);

        if (($config['filters']['limit'] > 0) && empty($productId)) {
            $pages = ceil($size / $config['filters']['limit']);
        }

        if ($type == 'preview') {
            $pages = $page;
        }

        do {
            $productCollection->setCurPage($page);
            $productCollection->load();

            $parentRelations = $this->productHelper->getParentsFromCollection($productCollection, $config);
            $parents = $this->productModel->getParents($parentRelations, $config);
            $processed += $this->getFeedData($productCollection, $parents, $config, $parentRelations, $data);

            if ($config['debug_memory']) {
                $this->feedHelper->addLog($page, $pages, $storeId);
            }

            $productCollection->clear();
            $parents = null;
            $page++;
        } while ($page <= $pages);

        $pageSize = isset($config['filters']['limit']) ? $config['filters']['limit'] : '';
        $summary = $this->feedHelper->getFeedSummary($timeStart, $processed, $pageSize);
        $footer = $this->sourceHelper->getXmlFromArray($summary, 'config');
        $this->feedHelper->writeFooter($footer);
        $this->feedHelper->validateAndMove($config, $type);
        $this->feedHelper->updateResult($storeId, $processed, $summary['time'], $summary['date'], $type, $pages);

        return [
            'status' => 'success',
            'qty'    => $processed,
            'time'   => $summary['time'],
            'path'   => $config['feed_locations']['full_path'],
            'url'    => $config['feed_locations']['url']
        ];
    }

    /**
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection $products
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection $parents
     * @param                                                          $config
     * @param                                                          $parentRelations
     * @param bool                                                     $data
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFeedData($products, $parents, $config, $parentRelations, $data = false)
    {
        $qty = 0;
        foreach ($products as $product) {
            $parent = null;
            if (!empty($parentRelations[$product->getEntityId()])) {
                foreach ($parentRelations[$product->getEntityId()] as $parentId) {
                    /** @var \Magento\Catalog\Model\Product $parent */
                    if ($parent = $parents->getItemById($parentId)) {
                        continue;
                    }
                }
            }

            $dataRow = $this->productHelper->getDataRow($product, $parent, $config);
            if (empty($dataRow)) {
                continue;
            }

            if ($feedRow = $this->sourceHelper->reformatData($dataRow, $product, $config, $parent)) {
                $this->feedHelper->writeRow($feedRow);
                $qty++;
            }

            if ($data) {
                $productData = $this->sourceHelper->getProductDataXml($product, 'product');
                $this->feedHelper->writeRow($productData);
                if ($parent) {
                    $parentData = $this->sourceHelper->getProductDataXml($parent, 'parent');
                    $this->feedHelper->writeRow($parentData);
                }
            }
        }

        return $qty;
    }
}
