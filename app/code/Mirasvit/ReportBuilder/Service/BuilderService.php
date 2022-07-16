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



namespace Mirasvit\ReportBuilder\Service;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponentFactory;
use Mirasvit\ReportBuilder\Api\Data\ReportInterface;
use Mirasvit\ReportBuilder\Model\ReportInstanceFactory;

class BuilderService
{
    /**
     * @var ReportInstanceFactory
     */
    private $reportInstanceFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var UiComponentFactory
     */
    private $uiComponentFactory;

    /**
     * BuilderService constructor.
     * @param ReportInstanceFactory $reportInstanceFactory
     * @param Registry $registry
     * @param UiComponentFactory $uiComponentFactory
     */
    public function __construct(
        ReportInstanceFactory $reportInstanceFactory,
        Registry $registry,
        UiComponentFactory $uiComponentFactory
    ) {
        $this->reportInstanceFactory = $reportInstanceFactory;
        $this->registry              = $registry;
        $this->uiComponentFactory    = $uiComponentFactory;
    }

    /**
     * @param ReportInterface $report
     * @return \Mirasvit\ReportBuilder\Model\ReportInstance
     */
    public function getReportInstance(ReportInterface $report)
    {
        $instance = $this->reportInstanceFactory->create();
        $instance->setName($report->getTitle())
            ->setIdentifier($report->getId());

        return $instance;
    }
}
