<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigData;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigDataCollectionFactory;
use Magmodules\GoogleShopping\Logger\GeneralLoggerInterface;
use Magmodules\GoogleShopping\Logger\ValidationLoggerInterface;

/**
 * Class General
 *
 * @package Magmodules\GoogleShopping\Helper
 */
class General extends AbstractHelper
{

    const MODULE_CODE = 'Magmodules_GoogleShopping';
    const XPATH_EXTENSION_ENABLED = 'magmodules_googleshopping/general/enable';
    const XPATH_GENERATE_ENABLED = 'magmodules_googleshopping/generate/enable';
    const XPATH_CRON_ENABLED = 'magmodules_googleshopping/generate/cron';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;
    /**
     * @var ProductMetadataInterface
     */
    private $metadata;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var ConfigDataCollectionFactory
     */
    private $configDataCollectionFactory;
    /**
     * @var ConfigData
     */
    private $coreDate;
    /**
     * @var TimezoneInterface
     */
    private $localeDate;
    /**
     * @var GeneralLoggerInterface
     */
    private $generalLogger;
    /**
     * @var ValidationLoggerInterface
     */
    private $validationLogger;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * General constructor.
     *
     * @param Context                     $context
     * @param StoreManagerInterface       $storeManager
     * @param ModuleListInterface         $moduleList
     * @param ProductMetadataInterface    $metadata
     * @param ConfigDataCollectionFactory $configDataCollectionFactory
     * @param ConfigData                  $config
     * @param DateTime                    $coreDate
     * @param TimezoneInterface           $localeDate
     * @param GeneralLoggerInterface      $generalLogger
     * @param ValidationLoggerInterface   $validationLogger
     * @param SerializerInterface         $serializer
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $metadata,
        ConfigDataCollectionFactory $configDataCollectionFactory,
        ConfigData $config,
        DateTime $coreDate,
        TimezoneInterface $localeDate,
        GeneralLoggerInterface $generalLogger,
        ValidationLoggerInterface $validationLogger,
        SerializerInterface $serializer
    ) {
        $this->storeManager = $storeManager;
        $this->moduleList = $moduleList;
        $this->metadata = $metadata;
        $this->configDataCollectionFactory = $configDataCollectionFactory;
        $this->config = $config;
        $this->coreDate = $coreDate;
        $this->localeDate = $localeDate;
        $this->generalLogger = $generalLogger;
        $this->validationLogger = $validationLogger;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function getCronEnabled()
    {
        return (boolean)$this->getStoreValue(self::XPATH_CRON_ENABLED);
    }

    /**
     * Get Configuration data.
     *
     * @param      $path
     * @param      $scope
     * @param null $storeId
     *
     * @return mixed
     */
    public function getStoreValue($path, $storeId = null, $scope = null)
    {
        if (empty($scope)) {
            $scope = ScopeInterface::SCOPE_STORE;
        }

        if (empty($storeId)) {
            try {
                $storeId = $this->storeManager->getStore()->getId();
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $storeId = 0;
            }
        }

        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * Get Configuration data.
     *
     * @param      $path
     * @param      $scope
     * @param null $storeId
     *
     * @return bool
     */
    public function isSetFlag($path, $storeId = null, $scope = null)
    {
        if (empty($scope)) {
            $scope = ScopeInterface::SCOPE_STORE;
        }

        if (empty($storeId)) {
            try {
                $storeId = $this->storeManager->getStore()->getId();
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $storeId = 0;
            }
        }

        return $this->scopeConfig->isSetFlag($path, $scope, $storeId);
    }

    /**
     * Get Configuration Array data.
     * Pre Magento 2.2.x => Unserialize
     * Magento 2.2.x and up => Json Decode
     *
     * @param      $path
     * @param null $storeId
     * @param null $scope
     *
     * @return array|mixed
     */
    public function getStoreValueArray($path, $storeId = null, $scope = null)
    {
        $value = $this->getStoreValue($path, $storeId, $scope);

        $result = json_decode($value, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            if (is_array($result)) {
                return $result;
            }
            return [];
        }

        try {
            $value = $this->serializer->unserialize($value);
        } catch (\InvalidArgumentException $e) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        return [];
    }

    /**
     * Get Uncached Value from core_config_data
     *
     * @param      $path
     * @param null $storeId
     *
     * @return mixed
     */
    public function getUncachedStoreValue($path, $storeId)
    {
        $collection = $this->configDataCollectionFactory->create()
            ->addFieldToSelect('value')
            ->addFieldToFilter('path', $path);

        if ($storeId > 0) {
            $collection->addFieldToFilter('scope_id', $storeId);
            $collection->addFieldToFilter('scope', 'stores');
        } else {
            $collection->addFieldToFilter('scope_id', 0);
            $collection->addFieldToFilter('scope', 'default');
        }

        $collection->getSelect()->limit(1);

        return $collection->getFirstItem()->getData('value');
    }

    /**
     * Set configuration data function.
     *
     * @param      $value
     * @param      $key
     * @param null $storeId
     */
    public function setConfigData($value, $key, $storeId = null)
    {
        if ($storeId) {
            $this->config->saveConfig($key, $value, 'stores', $storeId);
        } else {
            $this->config->saveConfig($key, $value, 'default', 0);
        }
    }

    /**
     * Returns current version of the extension.
     *
     * @return mixed
     */
    public function getExtensionVersion()
    {
        $moduleInfo = $this->moduleList->getOne(self::MODULE_CODE);

        return $moduleInfo['setup_version'];
    }

    /**
     * Returns current version of Magento.
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->metadata->getVersion();
    }

    /**
     * @param $path
     *
     * @return array
     */
    public function getEnabledArray($path)
    {
        $storeIds = [];
        if (!$this->getEnabled()) {
            return $storeIds;
        }

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            if ($this->getStoreValue($path, $store->getId())) {
                $storeIds[] = $store->getId();
            }
        }

        return $storeIds;
    }

    /**
     * General check if Extension is enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return (boolean)$this->getStoreValue(self::XPATH_EXTENSION_ENABLED);
    }

    /**
     * General check if Generation is enabled.
     *
     * @param null $storeId
     *
     * @return bool
     */
    public function getGenerateEnabled($storeId = null)
    {
        return (boolean)$this->getStoreValue(self::XPATH_GENERATE_ENABLED, $storeId);
    }

    /**
     * @param        $id
     * @param        $data
     * @param string $type
     */
    public function addTolog($id, $data, $type = 'module')
    {
        $debug = true;

        if ($type == 'module') {
            if ($debug) {
                $this->generalLogger->add($id, $data);
            }
        }

        if ($type == 'validation') {
            $this->validationLogger->add($id, $data);
        }
    }

    /**
     * @return string
     */
    public function getDateTime()
    {
        return $this->coreDate->date("Y-m-d H:i:s");
    }

    /**
     * @param $storeId
     *
     * @return mixed
     */
    public function getLocaleDate($storeId)
    {
        return $this->localeDate->scopeDate($storeId);
    }
}
