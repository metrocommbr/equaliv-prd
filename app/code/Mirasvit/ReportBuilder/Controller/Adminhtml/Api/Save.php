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
 * @package   mirasvit/module-report-builder
 * @version   1.1.2
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportBuilder\Controller\Adminhtml\Api;

use Magento\Backend\App\Action\Context;
use Mirasvit\Report\Controller\Adminhtml\Api\AbstractApi;
use Mirasvit\ReportBuilder\Repository\ReportRepository as BuilderReportRepository;

class Save extends AbstractApi
{
    /**
     * @var BuilderReportRepository
     */
    private $builderReportRepository;

    /**
     * Save constructor.
     * @param BuilderReportRepository $builderReportRepository
     * @param Context $context
     */
    public function __construct(
        BuilderReportRepository $builderReportRepository,
        Context $context
    ) {
        $this->builderReportRepository = $builderReportRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $request = $this->getRequest();

        $identifier = $request->getParam('identifier');

        $model = $this->builderReportRepository->get($identifier);

        $model->setName($request->getParam('title'));
        $model->setUserId($this->builderReportRepository->getUserId());

        $model->setColumns($request->getParam('columns', []))
            ->setDimensions($request->getParam('dimensions', []))
            ->setInternalFilters($request->getParam('internalFilters', []));

        $model->setPrimaryDimensions($request->getParam('primaryDimensions', []))
            ->setPrimaryFilters($request->getParam('primaryFilters', []));


        $this->builderReportRepository->save($model);

        /** @var \Magento\Framework\App\Response\Http $jsonResponse */
        $jsonResponse = $this->getResponse();
        $jsonResponse->representJson(\Zend_Json::encode([
            'success' => true,
            'message' => 'Report was saved.',
        ]));
    }
}
