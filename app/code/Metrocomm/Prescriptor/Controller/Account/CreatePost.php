<?php

namespace Metrocomm\Prescriptor\Controller\Account;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Metrocomm\Prescriptor\Model\PrescriptorFactory;

class CreatePost implements HttpPostActionInterface
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
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        RequestInterface $request,
        PrescriptorFactory $prescriptorFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->prescriptorFactory = $prescriptorFactory;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $data = $this->request->getParams();
        $email = $data['email'];
        $prescriptor = $this->prescriptorFactory->create();
        $resultRedirect = $this->redirectFactory->create();
        $resultRedirect->setUrl('/prescriptor/account/create');
        if (!$prescriptor->canCreate($email)) {
            $this->messageManager->addErrorMessage('Já existe um usuário com este email.');
            return $resultRedirect;
        }
        try {
            $websiteId = $this->storeManager->getWebsite()->getId();
            $prescriptor->setData($data);
            $prescriptor->setWebsiteId($websiteId);
            $prescriptor->save();
            $this->messageManager->addSuccessMessage('Cadastro realizado com sucesso. Você será avisado assim que for aprovado.');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Algo deu errado. Tente novamente mais tarde.');
        }
        return $resultRedirect;
    }
}
