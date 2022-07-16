<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Observer\Returns;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Registry;
use WeSupply\Toolbox\Model\ReturnsRepository;

/**
 * Class RegisterRefund
 *
 * @package WeSupply\Toolbox\Observer\Returns
 */
class RegisterRefund implements ObserverInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ReturnsRepository
     */
    protected $wsReturnsRepository;

    /**
     * RegisterRefund constructor.
     *
     * @param Registry          $registry
     * @param ReturnsRepository $wsReturnsRepository
     */
    public function __construct(
        Registry $registry,
        ReturnsRepository $wsReturnsRepository
    ) {
        $this->registry = $registry;
        $this->wsReturnsRepository = $wsReturnsRepository;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws AlreadyExistsException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $triggeredBy = $observer->getEvent()->getName();
        switch ($triggeredBy) {
            case 'sales_order_creditmemo_refund':
                $returnReference = $this->getReturnReference();
                $this->updateReturnByReferenceId($returnReference);
                break;
            case 'sales_order_payment_refund':
                $this->registerPayment($observer);
                break;
            case 'adminhtml_sales_order_creditmemo_register_before':
                $input =  $observer->getEvent()->getInput();
                $this->registerReturnRequest($input);
                break;
        }

        return $this;
    }

    /**
     * @param $input
     *
     * @throws AlreadyExistsException
     */
    private function registerReturnRequest($input)
    {
        $this->registry->unregister('return_reference');
        $this->registry->unregister('refund_amount');

        if (!empty($input['return_reference'])) {
            $this->registry->register('return_reference', $input['return_reference']);
            $return = $this->wsReturnsRepository->getByReturnReferenceId($input['return_reference']);

            if (!$return->getId()) {
                $this->wsReturnsRepository->registerNewReturn(
                    $input['return_reference'],
                    $input['request_log_id']
                );
            }
        }
    }

    /**
     * @param $observer
     */
    private function registerPayment($observer)
    {
        $returnReference = $this->getReturnReference();
        if (!empty($returnReference)) {
            $payment =  $observer->getEvent()->getPayment();
            $this->registry->register('refund_amount', $payment->getAmountRefunded());
        }
    }

    /**
     * @param $referenceId
     *
     * @throws \Exception
     */
    private function updateReturnByReferenceId($referenceId)
    {
        $refundAmount = $this->getRefundAmount();
        if (!empty($refundAmount)) {
            $this->wsReturnsRepository->updateReturn(
                $referenceId,
                [
                    'status' => 'done',
                    'refunded' => true
                ]
            );

        }
    }

    /**
     * @return mixed|string
     */
    private function getReturnReference()
    {
        return $this->registry->registry('return_reference') ?? '';
    }

    /**
     * @return mixed|string
     */
    private function getRefundAmount()
    {
        return $this->registry->registry('refund_amount') ?? '';
    }
}
