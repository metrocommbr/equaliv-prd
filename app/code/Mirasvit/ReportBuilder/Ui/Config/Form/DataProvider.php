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



namespace Mirasvit\ReportBuilder\Ui\Config\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\ReportBuilder\Api\Data\ConfigInterface;
use Mirasvit\ReportBuilder\Api\Repository\ConfigRepositoryInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepository;

    /**
     * DataProvider constructor.
     * @param ConfigRepositoryInterface $configRepository
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->configRepository = $configRepository;
        $this->collection = $configRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];

        foreach ($this->collection as $config) {
            $data[$config->getId()] = [
                ConfigInterface::ID     => $config->getId(),
                ConfigInterface::TITLE  => $config->getTitle(),
                ConfigInterface::CONFIG => $config->getConfig(),
            ];
        }

        return $data;
    }
}
