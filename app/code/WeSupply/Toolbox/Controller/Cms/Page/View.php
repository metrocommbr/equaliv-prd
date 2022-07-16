<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Controller\Cms\Page;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Cms\Model\Page;
use Magento\Csp\Api\CspAwareActionInterface;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use WeSupply\Toolbox\Helper\Data as WsHelper;

/**
 * Class View
 * @package WeSupply\Toolbox\Controller\Cms\Page
 */
class View extends \Magento\Cms\Controller\Page\View implements CspAwareActionInterface
{
    /**
     * @var WsHelper
     */
    protected $_helper;

    /**
     * @var Page
     */
    protected $_page;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param PageHelper $pageHelper
     * @param ForwardFactory $resultForwardFactory
     * @param Page $page
     * @param WsHelper $wsHelper
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        PageHelper $pageHelper,
        ForwardFactory $resultForwardFactory,
        Page $page,
        WsHelper $wsHelper
    )
    {
        $this->_page = $page;
        $this->_helper = $wsHelper;

        parent::__construct($context, $request, $pageHelper, $resultForwardFactory);
    }

    /**
     * @param array $appliedPolicies
     * @return array
     */
    public function modifyCsp(array $appliedPolicies): array
    {
        if (
            (strpos($this->_helper->getStoreLocatorIdentifier(), $this->_page->getIdentifier()) !== false ||
             strpos($this->_helper->getStoreDetailsIdentifier(), $this->_page->getIdentifier()) !== false)
            && $this->_helper->weSupplyHasDomainAlias()
        ) {
            $appliedPolicies[] = new FetchPolicy(
                'frame-src',
                false,
                [$this->_helper->getWesupplyFullDomain()],
                [$this->_helper->getProtocol()]
            );
        }

        return $appliedPolicies;
    }
}
