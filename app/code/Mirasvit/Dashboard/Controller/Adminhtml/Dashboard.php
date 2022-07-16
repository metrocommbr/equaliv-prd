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



namespace Mirasvit\Dashboard\Controller\Adminhtml;

use Magento\Backend\App\Action;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mirasvit\Dashboard\Repository\BoardRepository;

abstract class Dashboard extends Action
{
    /**
     * @var BoardRepository
     */
    protected $boardRepository;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * Dashboard constructor.
     * @param BoardRepository $boardRepository
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        BoardRepository $boardRepository,
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->boardRepository = $boardRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Dashboard::dashboard');
    }
}
