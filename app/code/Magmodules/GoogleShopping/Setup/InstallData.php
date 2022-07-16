<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 *
 * @package Magmodules\GoogleShopping\Setup
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * InstallData constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $categorySetup->addAttribute(
            Category::ENTITY,
            'googleshopping_cat',
            [
                'type'         => 'varchar',
                'label'        => 'Google Shopping Category',
                'input'        => 'text',
                'group'        => 'General Information',
                'global'       => 1,
                'visible'      => true,
                'required'     => false,
                'user_defined' => false,
                'sort_order'   => 100,
                'default'      => null
            ]
        );
    }
}
