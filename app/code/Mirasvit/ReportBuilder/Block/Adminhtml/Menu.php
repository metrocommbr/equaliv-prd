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



namespace Mirasvit\ReportBuilder\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;
use Mirasvit\ReportBuilder\Api\Data\ConfigInterface;
use Mirasvit\ReportBuilder\Api\Repository\ConfigRepositoryInterface;
use Mirasvit\ReportBuilder\Api\Repository\ReportRepositoryInterface;

class Menu extends AbstractMenu
{
    /**
     * @var ConfigRepositoryInterface
     */
    protected $reportRepository;

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepository;

    /**
     * Menu constructor.
     * @param ReportRepositoryInterface $reportRepository
     * @param ConfigRepositoryInterface $configRepository
     * @param Context $context
     */
    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ConfigRepositoryInterface $configRepository,
        Context $context
    ) {
        $this->reportRepository = $reportRepository;
        $this->configRepository = $configRepository;

        $this->visibleAt(['reportBuilder']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'id'       => 'config',
            'resource' => 'Mirasvit_ReportBuilder::configBuilder',
            'title'    => __('Config Builder'),
            'url'      => $this->urlBuilder->getUrl('reportBuilder/config'),
        ])->addItem([
            'resource' => 'Mirasvit_ReportBuilder::configBuilder',
            'title'    => __('Add New Config'),
            'url'      => $this->urlBuilder->getUrl('reportBuilder/config/new'),
        ], 'config');

        foreach ($this->configRepository->getCollection() as $config) {
            $this->addItem([
                'resource' => 'Mirasvit_ReportBuilder::configBuilder',
                'title'    => $config->getTitle(),
                'url'      => $this->urlBuilder->getUrl('reportBuilder/config/edit', [
                    ConfigInterface::ID => $config->getId(),
                ]),
            ], 'config');
        }

        return $this;
    }
}
