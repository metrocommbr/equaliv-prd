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



namespace Mirasvit\ReportBuilder\Controller\Adminhtml\Api;

use Magento\Backend\App\Action\Context;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;
use Mirasvit\Report\Controller\Adminhtml\Api\AbstractApi;
use Mirasvit\ReportBuilder\Repository\ReportRepository as BuilderReportRepository;

class Duplicate extends AbstractApi
{
    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    /**
     * @var BuilderReportRepository
     */
    private $builderReportRepository;

    /**
     * Duplicate constructor.
     * @param ReportRepositoryInterface $reportRepository
     * @param BuilderReportRepository $builderReportRepository
     * @param Context $context
     */
    public function __construct(
        ReportRepositoryInterface $reportRepository,
        BuilderReportRepository $builderReportRepository,
        Context $context
    ) {
        $this->reportRepository        = $reportRepository;
        $this->builderReportRepository = $builderReportRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $identifier = $this->getRequest()->getParam('identifier');

        $report = $this->reportRepository->get($identifier);

        if (!$report) {
            $this->messageManager->addSuccessMessage(__('This report no longer exists.'));

            return $resultRedirect->setRefererUrl();
        }

        $model = $this->builderReportRepository->create();
        $model->setName('Untitled Report')
            ->setUserId($this->builderReportRepository->getUserId())
            ->setColumns($report->getColumns())
            ->setDimensions($report->getDimensions())
            ->setPrimaryDimensions($report->getPrimaryDimensions())
            ->setPrimaryFilters($report->getPrimaryFilters());

        $this->builderReportRepository->save($model);

        return $resultRedirect->setPath('reports/report/view', ['report' => $model->getId()]);
    }
}
