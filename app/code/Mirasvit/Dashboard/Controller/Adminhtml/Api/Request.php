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



namespace Mirasvit\Dashboard\Controller\Adminhtml\Api;

use Magento\Backend\App\Action;
use Mirasvit\Dashboard\Api\Data\BoardInterface;
use Mirasvit\Dashboard\Model\Block;
use Mirasvit\Dashboard\Repository\BoardRepository;
use Mirasvit\Dashboard\Service\BlockService;
use Mirasvit\Report\Api\Service\CastingServiceInterface;
use Mirasvit\Report\Controller\Adminhtml\Api\AbstractApi;

class Request extends AbstractApi
{
    /**
     * @var BoardRepository
     */
    private $boardRepository;

    /**
     * @var CastingServiceInterface
     */
    private $castingService;

    /**
     * @var BlockService
     */
    private $blockService;

    /**
     * Request constructor.
     * @param BoardRepository $boardRepository
     * @param CastingServiceInterface $castingService
     * @param BlockService $blockService
     * @param Action\Context $context
     */
    public function __construct(
        BoardRepository $boardRepository,
        CastingServiceInterface $castingService,
        BlockService $blockService,
        Action\Context $context
    ) {
        $this->boardRepository = $boardRepository;
        $this->castingService  = $castingService;
        $this->blockService    = $blockService;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $token = $this->getRequest()->getParam('token');

        /** @var BoardInterface $board */
        $board = $this->boardRepository->getCollection()
            ->addFieldToFilter(BoardInterface::MOBILE_TOKEN, $token)
            ->addFieldToFilter(BoardInterface::IS_MOBILE_ENABLED, true)
            ->getFirstItem();

        if ($board->getId()) {
            /** @var \Magento\Framework\App\Request\Http $request */
            $request->setDispatched(true);
            $request->setActionName('request');
        }

        return parent::dispatch($request);
    }

    /**
     * @return \Magento\Framework\App\Response\Http|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\App\Response\Http $jsonResponse */
        $jsonResponse = $this->getResponse();

        try {
            $params = $this->castingService->toUnderscore($this->getRequest()->getParams());

            $block = new Block($params['block']);

            $response = $this->blockService->getApiResponse($block, $params['filters']);

            if (!$response) {
                return false;
            }

            $responseData = $response->toArray();

            return $jsonResponse->representJson(\Zend_Json::encode([
                'success' => true,
                'data'    => $responseData,
            ]));
        } catch (\Exception $e) {
            return $jsonResponse->representJson(\Zend_Json::encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        }
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return true;
    }
}
