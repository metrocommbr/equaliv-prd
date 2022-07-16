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
 * @package   mirasvit/module-dashboard
 * @version   1.3.2
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Dashboard\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Dashboard\Api\Data\BoardInterface;
use Mirasvit\Dashboard\Repository\BoardRepository;

class Mobile extends Action
{
    /**
     * @var BoardRepository
     */
    protected $boardRepository;

    /**
     * Mobile constructor.
     * @param BoardRepository $boardRepository
     * @param Context $context
     */
    public function __construct(
        BoardRepository $boardRepository,
        Context $context
    ) {
        $this->boardRepository = $boardRepository;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $token = $this->getRequest()->getParam('token');

        /** @var BoardInterface $board */
        $board = $this->boardRepository->getCollection()
            ->addFieldToFilter(BoardInterface::MOBILE_TOKEN, $token)
            ->addFieldToFilter(BoardInterface::IS_MOBILE_ENABLED, true)
            ->getFirstItem();

        if (!$board->getId()) {
            throw new \Exception('Access denied');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->getConfig()->getTitle()->prepend('Dashboard');

        return $resultPage;
    }
}
