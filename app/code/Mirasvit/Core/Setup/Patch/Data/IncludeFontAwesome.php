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
 * @package   mirasvit/module-core
 * @version   1.4.4
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class IncludeFontAwesome implements DataPatchInterface
{
    const NEW_PREFIX = 'mst_';

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        //this script does not overwrite existing data
        $this->setup->getConnection()->startSetup();
        $installer = $this->setup;
        $tableName       = $installer->getTable('core_config_data');
        $configsToUpdate = [
            'core/css/include_font_awesome' => 1,
        ];
        foreach ($configsToUpdate as $path => $default) {
            $select = $installer->getConnection()->select();
            $select->from($tableName, ['value'])
                ->where('path = ?', $path)
                ->where('scope_id = 0')
                ->where('scope = ?', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

            $value = $installer->getConnection()->fetchOne($select);
            $installer->getConnection()->insertOnDuplicate($tableName, [
                'path'     => self::NEW_PREFIX . $path,
                'value'    => $value !== false ? $value : $default,
                'scope'    => ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                'scope_id' => 0,
            ]);
        }
        $installer->getConnection()->delete($installer->getTable('core_config_data'),
            "path = 'mst_core/logger/developer_ip'"
        );
        $this->setup->getConnection()->endSetup();
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
