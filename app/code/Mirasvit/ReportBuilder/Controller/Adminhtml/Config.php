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



namespace Mirasvit\ReportBuilder\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\ReportBuilder\Api\Data\ConfigInterface;
use Mirasvit\ReportBuilder\Api\Repository\ConfigRepositoryInterface;
use Mirasvit\ReportBuilder\Service\BuilderService;

abstract class Config extends Action
{
    /**
     * @var ConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * @var BuilderService
     */
    protected $builderService;

    /**
     * @var Context
     */
    protected $context;


    /**
     * Config constructor.
     * @param ConfigRepositoryInterface $configRepository
     * @param BuilderService $builderService
     * @param Context $context
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        BuilderService $builderService,
        Context $context
    ) {
        $this->configRepository = $configRepository;
        $this->builderService = $builderService;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->getConfig()->getTitle()->prepend(__('Config Builder'));

        return $resultPage;
    }

    /**
     * @return ConfigInterface
     */
    public function initModel()
    {
        $model = $this->configRepository->create();

        if ($this->getRequest()->getParam(ConfigInterface::ID)) {
            $model = $this->configRepository->get($this->getRequest()->getParam(ConfigInterface::ID));
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_ReportBuilder::configBuilder');
    }
}
