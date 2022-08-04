<?php

namespace Metrocomm\Prescriptor\Controller\Adminhtml\Account;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Metrocomm\Prescriptor\Model\PrescriptorFactory;

class Approve implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var PrescriptorFactory
     */
    private $prescriptorFactory;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    public function __construct(
        RedirectFactory $redirectFactory,
        PrescriptorFactory $prescriptorFactory,
        RequestInterface $request
    ) {
        $this->request = $request;
        $this->prescriptorFactory = $prescriptorFactory;
        $this->redirectFactory = $redirectFactory;
    }

    public function execute()
    {
        $id = $this->request->getParam('entity_id');
        $resultRedirect = $this->redirectFactory->create();

        $prescriptor = $this->prescriptorFactory->create()->load($id);
        $prescriptor->convert();

        return $resultRedirect->setPath('*/*/');

    }
}
