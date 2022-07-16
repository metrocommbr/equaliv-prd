<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Returns
 *
 * @package WeSupply\Toolbox\Model
 */
class Returns extends AbstractModel implements IdentityInterface
{
    /**
     * Returns cache tag
     */
    const CACHE_TAG = 'wesupply_returns';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'wesupply_returns';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\WeSupply\Toolbox\Model\ResourceModel\Returns::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return bool
     */
    public function getRefunded()
    {
        return (bool) $this->getData('refunded');
    }

    /**
     * @return string
     */
    public function getRequestLogId()
    {
        return $this->getData('request_log_id');
    }

    /**
     * @return string
     */
    public function getCreditMemoId()
    {
        return $this->getData('creditmemo_id');
    }

    /**
     * @param $referenceId
     */
    public function setReturnReference($referenceId)
    {
        $this->setData('return_reference', $referenceId);
    }

    /**
     * @param $requestLogId
     */
    public function setRequestLogId($requestLogId)
    {
        $this->setData('request_log_id', $requestLogId);
    }

    /**
     * @param $creditMemoId
     */
    public function setCreditMemoId($creditMemoId)
    {
        $this->setData('creditmemo_id', $creditMemoId);
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->setData('status', $status);
    }

    /**
     * @param $refunded
     */
    public function setRefunded($refunded)
    {
        $this->setData('refunded', $refunded);
    }
}
