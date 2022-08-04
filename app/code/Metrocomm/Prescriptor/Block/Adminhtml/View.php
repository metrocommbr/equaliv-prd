<?php

namespace Metrocomm\Prescriptor\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Metrocomm\Prescriptor\Model\PrescriptorFactory;

class View extends Template
{
    /**
     * @var PrescriptorFactory
     */
    private $prescriptorFactory;
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request,
        PrescriptorFactory $prescriptorFactory,
        Template\Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->prescriptorFactory = $prescriptorFactory;
        $this->request = $request;
    }

    public function getPrescriptor()
    {
        $id = $this->request->getParam('entity_id');
        return $this->prescriptorFactory->create()->load($id);
    }
}
