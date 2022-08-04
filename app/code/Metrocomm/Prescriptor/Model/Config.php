<?php

namespace Metrocomm\Prescriptor\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const XML_GROUP_PATH = 'prescriptor/general/customer_group';
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {

        $this->scopeConfig = $scopeConfig;
    }

    public function getCustomerGroup()
    {
        return $this->scopeConfig->getValue(self::XML_GROUP_PATH);
    }
}
