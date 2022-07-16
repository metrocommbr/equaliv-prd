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


namespace Mirasvit\Dashboard\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Mirasvit\Dashboard\Api\Data\BlockInterface;
use Mirasvit\Dashboard\Api\Repository\BoardRepositoryInterface;
use Mirasvit\Dashboard\Model\Block;

class MigrateBoards implements DataPatchInterface
{
    const NEW_PREFIX = 'mst_';

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var BoardRepositoryInterface
     */
    private $boardRepository;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    public function __construct(
        ModuleDataSetupInterface $setup,
        BoardRepositoryInterface $boardRepository,
        ArrayManager $arrayManager
    ) {
        $this->setup = $setup;
        $this->boardRepository = $boardRepository;
        $this->arrayManager    = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        //this script does not overwrite existing data
        $this->setup->getConnection()->startSetup();

        foreach ($this->boardRepository->getCollection() as $board) {
            $blocks = $board->getBlocks();
            /** @var Block $block */
            foreach ($blocks as $key => $block) {
                if (!$block->getData(BlockInterface::CONFIG)) {
                    continue;
                }

                $data = $block->getData();

                $this->replace($data, 'renderer', 'config/renderer');

                $this->replace($data, 'report/data', 'config/single/column');
                $this->replace($data, 'single/sparkline/isActive', 'config/single/sparkLine');

                $block->setData($data);
                $blocks[$key] = $block;
            }

            $board->setBlocks($blocks);

            $this->boardRepository->save($board);
        }

        $this->setup->getConnection()->endSetup();
    }

    /**
     * @param array  $data
     * @param string $oldPath
     * @param string $newPath
     */
    private function replace(&$data, $oldPath, $newPath)
    {
        $oldValue = $this->arrayManager->get($oldPath, $data);
        $newValue = $this->arrayManager->get($newPath, $data);

        if ($oldValue && !$newValue) {
            $data = $this->arrayManager->set($newPath, $data, $oldValue);
        }
        $data = $this->arrayManager->remove($oldPath, $data);
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
