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



namespace Mirasvit\ReportBuilder\Plugin;

use Mirasvit\ReportApi\Config\Loader\Reader;
use Mirasvit\ReportBuilder\Api\Repository\ConfigRepositoryInterface;

class ReaderPlugin
{
    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepository;

    /**
     * ReaderPlugin constructor.
     * @param ConfigRepositoryInterface $configRepository
     */
    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * Add report configs stored in DB.
     *
     * @param Reader $subject
     * @param array  $fileList
     *
     * @return array
     */
    public function afterGetFiles(Reader $subject, array $fileList = [])
    {
        foreach ($this->configRepository->getCollection() as $config) {
            $key = 'mst_report_' . $config->getId();
            $fileList[$key] = $config->getConfig();
        }

        return $fileList;
    }
}
