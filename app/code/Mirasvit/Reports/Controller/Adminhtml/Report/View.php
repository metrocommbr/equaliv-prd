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
 * @package   mirasvit/module-reports
 * @version   1.4.4
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Reports\Controller\Adminhtml\Report;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Reports\Controller\Adminhtml\AbstractReport;

class View extends AbstractReport
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $report = $this->manager->getReport();

        if (!$report) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('reports/report/view');
        }

        $this->registry->register('current_report', $report);

        $this->initPage($resultPage)
            ->getConfig()->getTitle()->prepend($report->getName());

        return $resultPage;
    }
}
