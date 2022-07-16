<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Controller\Adminhtml\Actions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magmodules\GoogleShopping\Helper\Feed as FeedHelper;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;

/**
 * Class Download
 *
 * @package Magmodules\GoogleShopping\Controller\Adminhtml\Actions
 */
class Download extends Action
{

    /**
     * @var FeedHelper
     */
    private $feedHelper;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;
    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * Download constructor.
     *
     * @param Context       $context
     * @param RawFactory    $resultRawFactory
     * @param FileFactory   $fileFactory
     * @param FeedHelper    $feedHelper
     * @param GeneralHelper $generalHelper
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        FileFactory $fileFactory,
        FeedHelper $feedHelper,
        GeneralHelper $generalHelper,
        DirectoryList $directoryList
    ) {
        $this->feedHelper = $feedHelper;
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->generalHelper = $generalHelper;
        parent::__construct($context);
    }

    /**
     * Execute function for download of the GoogleShopping feed in admin.
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        $feed = $this->feedHelper->getFeedLocation($storeId);

        if (!empty($feed['full_path']) && file_exists($feed['full_path'])) {
            try {
                $this->fileFactory->create(
                    basename($feed['full_path']),
                    [
                        'type'  => 'filename',
                        'value' => 'googleshopping/' . basename($feed['full_path']),
                        'rm'    => false,
                    ],
                    DirectoryList::MEDIA,
                    'application/octet-stream',
                    null
                );
                $resultRaw = $this->resultRawFactory->create();
                return $resultRaw;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t generate the feed right now, please check error log in /var/log/googleshopping.log')
                );
                $this->generalHelper->addTolog('Generate', $e->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('File not found, please generate new feed.'));
        $this->_redirect('adminhtml/system_config/edit/section/magmodules_googleshopping');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magmodules_GoogleShopping::config');
    }
}
