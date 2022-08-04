<?php

namespace Metrocomm\Prescriptor\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Prescriptor extends AbstractModel
{
    /**
     * @var CustomerInterface
     */
    private $customer;
    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var AddressFactory
     */
    private $addressFactory;

    public function __construct(
        AddressFactory $addressFactory,
        ManagerInterface $messageManager,
        CollectionFactory $collectionFactory,
        Config $config,
        AccountManagementInterface $accountManagement,
        CustomerInterface $customer,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->customer = $customer;
        $this->accountManagement = $accountManagement;
        $this->config = $config;
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
        $this->addressFactory = $addressFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Prescriptor::class);
    }

    public function convert()
    {
        $this->customer->setFirstname($this->getData('firstname'));
        $this->customer->setLastname($this->getData('lastname'));
        $this->customer->setEmail($this->getData('email'));
        $this->customer->setWebsiteId($this->getData('website_id'));
        $this->customer->setDob($this->getData('dob'));
        $this->customer->setGroupId($this->config->getCustomerGroup());
        try {
            $newCustomer = $this->accountManagement->createAccount($this->customer);
            $newAddress = $this->addressFactory->create();
            $fullStreet = [
                $this->getData('street'),
                $this->getData('number'),
                $this->getData('complement'),
                $this->getData('neighborhood'),

            ];
            $newAddress->setCustomerId($newCustomer->getId());
            $newAddress->setFirstname($this->getData('firstname'));
            $newAddress->setLastname($this->getData('lastname'));
            $newAddress->setStreetFull($fullStreet);
            $newAddress->setPostcode($this->getData('postcode'));
            $newAddress->setCity($this->getData('city'));
            $newAddress->setCountryId($this->getData('country'));
            $newAddress->setTelephone($this->getData('telephone'));
            $newAddress->setIsDefaultBilling(true);
            $newAddress->setIsDefaultShipping(true);
            $newAddress->setSaveInAddressBook(true);
            $newAddress->save();
            $this->setData('status', 'approved');
            $this->messageManager->addSuccessMessage('Cliente criado com sucesso.');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->setData('status', 'error');
        }
        $this->save();
    }

    public function canCreate($email): bool
    {
        $customerCollection = $this->collectionFactory->create()->addFieldToFilter('email', $email);
        return !$customerCollection->getSize();
    }

    public function getName()
    {
        return $this->getData('firstname') . ' ' . $this->getData('lastname');
    }
    public function getFullStreet()
    {
        return $this->getData('street') . ', '
            . $this->getData('number') . ' - '
            . $this->getData('complement');
    }
}
