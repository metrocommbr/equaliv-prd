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



namespace Mirasvit\Reports\Cron;

use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory as AddressCollectionFactory;
use Mirasvit\Reports\Model\PostcodeFactory;

class PostcodeUnknown
{
    /**
     * @var PostcodeFactory
     */
    protected $postcodeFactory;

    /**
     * @var AddressCollectionFactory
     */
    protected $addressCollectionFactory;

    /**
     * @param PostcodeFactory          $postcodeFactory
     * @param AddressCollectionFactory $addressCollectionFactory
     */
    public function __construct(
        PostcodeFactory $postcodeFactory,
        AddressCollectionFactory $addressCollectionFactory
    ) {
        $this->postcodeFactory          = $postcodeFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
    }

    /**
     * @param bool $verbose
     *
     * @return void
     */
    public function execute($verbose = false)
    {
        $collection = $this->addressCollectionFactory->create();
        $collection->setPageSize(100);

        $pages = $collection->getLastPageNumber();
        $page  = 1;

        do {
            $collection->setCurPage($page);
            $collection->load();

            foreach ($collection as $row) {
                $countryId = $row->getCountryId();
                $postcode  = $row->getPostcode();

                if (trim($postcode) == '' || trim($countryId) == '') {
                    continue;
                }

                $model = $this->postcodeFactory->create();
                if (!$model->loadByCode($countryId, $postcode)) {
                    $model->setCountryId($countryId)
                        ->setPostcode($postcode)
                        ->save();
                }
            }

            ++$page;
            $collection->clear();

        } while ($page <= $pages);
    }
}