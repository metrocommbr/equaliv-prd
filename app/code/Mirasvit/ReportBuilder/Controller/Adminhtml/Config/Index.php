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



namespace Mirasvit\ReportBuilder\Controller\Adminhtml\Config;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\ReportBuilder\Api\Data\ConfigInterface;
use Mirasvit\ReportBuilder\Controller\Adminhtml\Config;

class Index extends Config
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $emptyReports = $this->configRepository->getCollection()
            ->addFieldToFilter(ConfigInterface::TITLE, "");

        foreach ($emptyReports as $config) {
            $this->configRepository->delete($config);
        }

        $this->initPage($resultPage);

        return $resultPage;
    }
}
