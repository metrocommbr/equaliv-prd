<?php

namespace Metrocomm\Prescriptor\Controller\Adminhtml\Account;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class View implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    public function __construct(
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        return $this->pageFactory->create();
    }
}
