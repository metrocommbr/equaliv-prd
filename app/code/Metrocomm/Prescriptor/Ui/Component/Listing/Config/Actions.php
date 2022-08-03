<?php

namespace Metrocomm\Prescriptor\Ui\Component\Listing\Config;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    /** Url path */
    const ROW_APPROVE_URL = 'prescriptor/account/approve';

    /** Url path */
    const ROW_DISAPPROVE_URL = 'prescriptor/account/disapprove';

    /** Url path */
    const ROW_VIEW_URL = 'prescriptor/account/view';

    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $_approveUrl;

    /**
     * @var string
     */
    private $_disapproveUrl;

    /**
     * @var string
     */
    private $_viewUrl;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $approveUrl = self::ROW_APPROVE_URL,
        $disapproveUrl = self::ROW_DISAPPROVE_URL,
        $viewUrl = self::ROW_VIEW_URL
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_approveUrl = $approveUrl;
        $this->_disapproveUrl = $disapproveUrl;
        $this->_viewUrl = $viewUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $item[$name]['approve'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_approveUrl,
                            ['entity_id' => $item['entity_id']]
                        ),
                        'label' => __('Approve'),
                    ];
                    $item[$name]['disapprove'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_disapproveUrl,
                            ['entity_id' => $item['entity_id']]
                        ),
                        'label' => __('Disapprove'),
                    ];
                    $item[$name]['view'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_viewUrl,
                            ['entity_id' => $item['entity_id']]
                        ),
                        'target' => '_blank',
                        'label' => __('View'),
                    ];

                }
            }
        }

        return $dataSource;
    }
}
