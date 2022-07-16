<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;

/**
 * Class Uninstall
 *
 * @package Magmodules\GoogleShopping\Setup
 */
class Uninstall implements UninstallInterface
{

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Uninstall constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetupManager */
        $categorySetupManager = $this->categorySetupFactory->create();
        $categorySetupManager->removeAttribute(Product::ENTITY, 'googleshopping_exclude');
        $categorySetupManager->removeAttribute(Product::ENTITY, 'googleshopping_category');
        $categorySetupManager->removeAttribute(Category::ENTITY, 'googleshopping_cat');

        $entityTypeId = $categorySetupManager->getEntityTypeId('catalog_product');
        $attributeSetIds = $categorySetupManager->getAllAttributeSetIds($entityTypeId);

        foreach ($attributeSetIds as $attributeSetId) {
            $categorySetupManager->removeAttributeGroup($entityTypeId, $attributeSetId, 'Google Shopping');
        }

        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            ['path LIKE ?' => 'magmodules_googleshopping/%']
        );

        $setup->endSetup();
    }
}
