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

use Mirasvit\ReportBuilder\Api\Data\ConfigInterface;
use Mirasvit\ReportBuilder\Controller\Adminhtml\Config;

class Save extends Config
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getParams()) {
            $config = $this->initModel();

            $configuration = $this->getRequest()->getParam('config', []);

            $config->setConfig($configuration)
                ->setTitle($this->getRequest()->getParam('title'))
                ->setUserId($this->configRepository->getUserId());

            try {
                $this->configRepository->save($config);

                $this->messageManager->addSuccessMessage(__('You saved the config.'));

                return $resultRedirect->setPath('*/*/edit', [ConfigInterface::ID => $config->getId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [ConfigInterface::ID => $config->getId()]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }
}
