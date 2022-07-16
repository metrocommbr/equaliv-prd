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



namespace Mirasvit\Reports\Controller\Adminhtml\Geo;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Reports\Model\Postcode;

class ProcessImport extends Action
{
    /**
     * @var Postcode
     */
    protected $postcode;
    /**
     * @var Context
     */
    protected $context;

    /**
     * ProcessImport constructor.
     * @param Postcode $postcode
     * @param Context $context
     */
    public function __construct(
        Postcode $postcode,
        Context $context
    ) {
        $this->postcode = $postcode;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $files = $this->getRequest()->getParam('files');
        if (is_array($files)) {
            foreach ($files as $file) {
                if ($this->postcode->importFile($file)) {
                    $this->messageManager->addSuccess(__('The file %1 has been imported.', $file));
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/import');
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_Reports::reports');

        $resultPage->getConfig()->getTitle()->prepend(__('Advanced Reports'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Reports::reports_view');
    }
}
