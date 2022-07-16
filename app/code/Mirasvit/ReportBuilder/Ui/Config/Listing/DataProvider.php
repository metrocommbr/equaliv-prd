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



namespace Mirasvit\ReportBuilder\Ui\Config\Listing;

use Magento\Framework\Api\Search\SearchResultInterface;
use Mirasvit\ReportBuilder\Api\Data\ConfigInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = [
            'items'        => [],
            'totalRecords' => $searchResult->getTotalCount(),
        ];

        /** @var ConfigInterface $item */
        foreach ($searchResult->getItems() as $item) {
            $data = [
                ConfigInterface::ID    => $item->getId(),
                ConfigInterface::TITLE => $item->getTitle(),
            ];

            $result['items'][] = $data;
        }

        return $result;
    }
}
