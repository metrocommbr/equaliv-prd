<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Category;
use Psr\Log\LoggerInterface;

/**
 * Class UpgradeData
 *
 * @package Magmodules\GoogleShopping\Setup
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var ValueInterface
     */
    private $configReader;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory          $eavSetupFactory
     * @param ProductMetadataInterface $productMetadata
     * @param ObjectManagerInterface   $objectManager
     * @param ValueInterface           $configReader
     * @param WriterInterface          $configWriter
     * @param ProductCollectionFactory $productCollectionFactory
     * @param LoggerInterface          $logger
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ProductMetadataInterface $productMetadata,
        ObjectManagerInterface $objectManager,
        ValueInterface $configReader,
        WriterInterface $configWriter,
        ProductCollectionFactory $productCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->productMetadata = $productMetadata;
        $this->objectManager = $objectManager;
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $this->addProductAtributes($setup);
            $this->changeConfigPaths();

            $magentoVersion = $this->productMetadata->getVersion();
            if (version_compare($magentoVersion, '2.2.0', '>=')) {
                $this->convertSerializedDataToJson($setup);
            }
        }

        if (version_compare($context->getVersion(), '1.0.10', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Category::ENTITY,
                'googleshopping_cat_exlude',
                [
                    'type'         => 'int',
                    'label'        => 'Disable Category from Product-Type',
                    'input'        => 'select',
                    'source'       => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'global'       => 1,
                    'visible'      => true,
                    'required'     => false,
                    'user_defined' => false,
                    'sort_order'   => 100,
                    'default'      => 0
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $attribute = $eavSetup->getAttribute(Product::ENTITY, 'googleshopping_exclude');
            if ($attribute) {
                $eavSetup->updateAttribute(Product::ENTITY, 'googleshopping_exclude', 'frontend_input', 'boolean');
                $eavSetup->updateAttribute(Product::ENTITY, 'googleshopping_exclude', 'default', '0');
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    'googleshopping_exclude',
                    'apply_to',
                    'simple,configurable,virtual,bundle,downloadable'
                );
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function addProductAtributes(ModuleDataSetupInterface $setup)
    {
        $groupName = 'Google Shopping';

        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $attributeSetIds = $eavSetup->getAllAttributeSetIds(Product::ENTITY);

        foreach ($attributeSetIds as $attributeSetId) {
            $eavSetup->addAttributeGroup(Product::ENTITY, $attributeSetId, $groupName, 1000);
        }

        $eavSetup->addAttribute(
            Product::ENTITY,
            'googleshopping_exclude',
            [
                'group'                   => $groupName,
                'type'                    => 'int',
                'label'                   => 'Exclude for Google Shopping',
                'input'                   => 'boolean',
                'source'                  => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'default'                 => '0',
                'user_defined'            => true,
                'required'                => false,
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => false,
                'unique'                  => false,
                'apply_to'                => 'simple,configurable,virtual,bundle,downloadable'
            ]
        );

        $attribute = $eavSetup->getAttribute(Product::ENTITY, 'googleshopping_exclude');
        foreach ($attributeSetIds as $attributeSetId) {
            $eavSetup->addAttributeToGroup(
                Product::ENTITY,
                $attributeSetId,
                $groupName,
                $attribute['attribute_id'],
                110
            );
        }

        $eavSetup->addAttribute(
            Product::ENTITY,
            'googleshopping_category',
            [
                'group'                   => $groupName,
                'type'                    => 'varchar',
                'label'                   => 'Google Shopping Product Category',
                'note'                    => 'Overwrite the Google Shopping Category from your category configuration and default configuration on product level with this open text field. You can implement the full path or the ID from the Google Shopping requirements.',
                'input'                   => 'text',
                'source'                  => '',
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => true,
                'default'                 => '',
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => false,
                'unique'                  => false,
            ]
        );

        $attribute = $eavSetup->getAttribute(Product::ENTITY, 'googleshopping_category');
        foreach ($attributeSetIds as $attributeSetId) {
            $eavSetup->addAttributeToGroup(
                Product::ENTITY,
                $attributeSetId,
                $groupName,
                $attribute['attribute_id'],
                111
            );
        }
    }

    /**
     * Change config paths for fields due to changes in config options.
     */
    public function changeConfigPaths()
    {
        $collection = $this->configReader->getCollection()
            ->addFieldToFilter("path", "magmodules_googleshopping/advanced/parent_atts");

        foreach ($collection as $config) {
            /** @var \Magento\Framework\App\Config\Value $config */
            $this->configWriter->save(
                "magmodules_googleshopping/types/configurable_parent_atts",
                $config->getValue(),
                $config->getScope(),
                $config->getScopeId()
            );
            $this->configWriter->delete(
                "magmodules_googleshopping/advanced/parent_atts",
                $config->getScope(),
                $config->getScopeId()
            );
        }

        $collection = $this->configReader->getCollection()
            ->addFieldToFilter("path", "magmodules_googleshopping/advanced/relations");

        foreach ($collection as $config) {
            /** @var \Magento\Framework\App\Config\Value $config */
            if ($config->getValue() == 1) {
                $this->configWriter->save(
                    "magmodules_googleshopping/types/configurable",
                    'simple',
                    $config->getScope(),
                    $config->getScopeId()
                );
            }
            $this->configWriter->delete(
                "magmodules_googleshopping/advanced/relations",
                $config->getScope(),
                $config->getScopeId()
            );
        }

        $collection = $this->configReader->getCollection()
            ->addFieldToFilter("path", "magmodules_googleshopping/general/enable")
            ->addFieldToFilter("scope_id", ["neq" => 0]);

        foreach ($collection as $config) {
            /** @var \Magento\Framework\App\Config\Value $config */
            $this->configWriter->delete(
                "magmodules_googleshopping/general/enable",
                $config->getScope(),
                $config->getScopeId()
            );
        }
    }

    /**
     * Convert Serialzed Data fields to Json for Magento 2.2
     * Using Object Manager for backwards compatability
     *
     * @param ModuleDataSetupInterface $setup
     */
    public function convertSerializedDataToJson(ModuleDataSetupInterface $setup)
    {
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $fieldDataConverter = $this->objectManager
            ->create(\Magento\Framework\DB\FieldDataConverterFactory::class)
            ->create(\Magento\Framework\DB\DataConverter\SerializedToJson::class);

        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $queryModifier = $this->objectManager
            ->create(\Magento\Framework\DB\Select\QueryModifierFactory::class)
            ->create(
                'in',
                [
                    'values' => [
                        'path' => [
                            'magmodules_googleshopping/advanced/extra_fields',
                            'magmodules_googleshopping/advanced/shipping',
                            'magmodules_googleshopping/filter/filters_data'
                        ]
                    ]
                ]
            );

        $fieldDataConverter->convert(
            $setup->getConnection(),
            $setup->getTable('core_config_data'),
            'config_id',
            'value',
            $queryModifier
        );
    }
}
