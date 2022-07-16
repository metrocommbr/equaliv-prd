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
use Mirasvit\Report\Controller\Adminhtml\Api\AbstractApi;
use Mirasvit\ReportBuilder\Repository\ReportRepository as BuilderReportRepository;

class Delete extends AbstractApi
{
    /**
     * @var BuilderReportRepository
     */
    private $builderReportRepository;

    /**
     * Delete constructor.
     * @param BuilderReportRepository $builderReportRepository
     * @param Context $context
     */
    public function __construct(
        BuilderReportRepository $builderReportRepository,
        Context $context
    ) {
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

        $model = $this->builderReportRepository->get($identifier);

        if ($model) {
            try {
                $this->builderReportRepository->delete($model);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('reports/report/view');
    }
}
