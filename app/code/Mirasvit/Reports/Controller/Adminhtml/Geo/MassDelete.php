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
 * @package   mirasvit/module-reports
 * @version   1.4.4
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mirasvit\Reports\Controller\Adminhtml\Geo;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Reports\Model\ResourceModel\Postcode\CollectionFactory as PostcodeCollectionFactory;

/**
 * Class MassDelete
 */
class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var PostcodeCollectionFactory
     */
    protected $postcodeCollectionFactory;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param PostcodeCollectionFactory $postcodeCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        PostcodeCollectionFactory $postcodeCollectionFactory
    ) {
        $this->filter = $filter;
        $this->postcodeCollectionFactory = $postcodeCollectionFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->postcodeCollectionFactory->create());

        $postcodeDeleted = 0;
        foreach ($collection->getItems() as $postcode) {
            $postcode->delete();
            $postcodeDeleted++;
        }
        $this->messageManager->addSuccess(
            __('Total of %1 record(s) were removed from the postcode table.', $postcodeDeleted)
        );

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/index');
    }
}
