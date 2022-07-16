<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use WeSupply\Toolbox\Model\ResourceModel\Returns as ReturnsResource;

/**
 * Class ReturnsRepository
 *
 * @package WeSupply\Toolbox\Model
 */
class ReturnsRepository
{
    /**
     * @var ReturnsResource
     */
    protected $resource;
    /**
     * @var ReturnsFactory
     */
    protected $returnsFactory;

    /**
     * @var Returns
     */
    private $return;

    /**
     * ReturnsRepository constructor.
     *
     * @param ReturnsResource $resource
     * @param ReturnsFactory  $returnsFactory
     */
    public function __construct(
        ReturnsResource $resource,
        ReturnsFactory $returnsFactory
    ) {
        $this->resource = $resource;
        $this->returnsFactory = $returnsFactory;
    }

    /**
     * @param $referenceId
     *
     * @return Returns
     */
    public function getByReturnReferenceId($referenceId)
    {
        $this->return = $this->returnsFactory->create();
        $this->resource->load($this->return, $referenceId, 'return_reference');

        return $this->return;
    }

    /**
     * @param $referenceId
     * @param $requestLogId
     *
     * @throws AlreadyExistsException
     */
    public function registerNewReturn($referenceId, $requestLogId)
    {
        $this->return = $this->returnsFactory->create();

        $this->return->setReturnReference($referenceId);
        $this->return->setRequestLogId($requestLogId);
        $this->return->setStatus('init');

        $this->resource->save($this->return);
    }

    /**
     * @param $referenceId
     * @param $returnData
     *
     * @throws \Exception
     */
    public function updateReturn($referenceId, $returnData)
    {
        $this->getByReturnReferenceId($referenceId);

        if ($this->return->getId()) {
            $this->return->setStatus($returnData['status']);
            $this->return->setRefunded($returnData['refunded']);

            $this->resource->save($this->return);
        }
    }

    /**
     * @param $referenceId
     * @param $creditmemoId
     *
     * @throws AlreadyExistsException
     */
    public function updateCreditmemoId($referenceId, $creditmemoId)
    {
        $this->getByReturnReferenceId($referenceId);

        if ($this->return->getId()) {
            $this->return->setCreditMemoId($creditmemoId);

            $this->resource->save($this->return);
        }
    }
}
