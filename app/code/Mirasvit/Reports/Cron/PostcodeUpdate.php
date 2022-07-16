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

use Mirasvit\Reports\Model\ResourceModel\Postcode\CollectionFactory as PostcodeCollectionFactory;
use Mirasvit\Reports\Service\PostcodeService;

class PostcodeUpdate
{
    /**
     * @var PostcodeCollectionFactory
     */
    private $postcodeCollectionFactory;

    /**
     * @var PostcodeService
     */
    private $postcodeService;

    /**
     * PostcodeUpdate constructor.
     * @param PostcodeCollectionFactory $postcodeCollectionFactory
     * @param PostcodeService $postcodeService
     */
    public function __construct(
        PostcodeCollectionFactory $postcodeCollectionFactory,
        PostcodeService $postcodeService
    ) {
        $this->postcodeCollectionFactory = $postcodeCollectionFactory;
        $this->postcodeService           = $postcodeService;
    }

    /**
     * @param bool $verbose
     *
     * @return void
     */
    public function execute($verbose = false)
    {
        $this->update($verbose);
        //        $this->batchMerge($verbose);
    }

    /**
     * @param bool $verbose
     * @throws \Exception
     */
    public function update($verbose = false)
    {
        $limit = 100;

        $lastId = 0;

        do {
            $collection = $this->postcodeCollectionFactory->create()
                ->addFieldToFilter('postcode_id', ['gt' => $lastId])
                ->setOrder('postcode_id', 'asc');

            $collection->getSelect()
                //                ->where('(original NOT LIKE "%api%" OR original IS NULL)')
                //                ->where('country_id <> "GB"')
                ->where('updated = 0');

            $collection->setPageSize(10);

            /** @var \Mirasvit\Reports\Model\Postcode $model */
            foreach ($collection as $model) {
                $location = $this->postcodeService->findLocation($model->getCountryId(), $model->getPostcode());

                $model->setState($location['state'])
                    ->setProvince($location['province'])
                    ->setPlace($location['place'])
                    ->setCommunity($location['community'])
                    ->setLat($location['lat'])
                    ->setLng($location['lng'])
                    ->setUpdated(1)
                    ->save();

                $lastId = $model->getId();
            }
        } while ($collection->count() > 0 && $limit > 0);
    }

    /**
     * @param bool $verbose
     * @throws \Exception
     */
    public function batchMerge($verbose = false)
    {
        $lastId = 0;

        do {
            $collection = $this->postcodeCollectionFactory->create()
                ->addFieldToFilter('postcode_id', ['gt' => $lastId])
                ->setOrder('postcode_id', 'asc');
            $collection->getSelect()
                ->where('original LIKE "%api%"')
                ->where('updated = 0');

            $collection->setPageSize(100);

            $collection->load();

            /** @var \Mirasvit\Reports\Model\Postcode $model */
            foreach ($collection as $model) {
                $data = json_decode($model->getOriginal(), true);

                if (!isset($data['api'][0])) {
                    $lastId = $model->getId();
                    continue;
                }

                $apiData = [
                    'state'     => false,
                    'province'  => false,
                    'place'     => false,
                    'community' => false,
                    'lat'       => false,
                    'lng'       => false,
                ];

                $apiData['place']    = $this->prettyPlace($data['api'][0]['adminArea5']);
                $apiData['province'] = $this->prettyPlace($data['api'][0]['adminArea4']);
                $apiData['state']    = $data['api'][0]['adminArea3'];
                $apiData['lat']      = $data['api'][0]['latLng']['lat'];
                $apiData['lng']      = $data['api'][0]['latLng']['lng'];

                $result = $apiData;

                $model->setState($result['state'])
                    ->setProvince($result['province'])
                    ->setPlace($result['place'])
                    ->setCommunity($result['community'])
                    ->setLat($result['lat'])
                    ->setLng($result['lng'])
                    ->setUpdated(1)
                    ->save();

                $lastId = $model->getId();
            }
        } while ($collection->count() > 0);
    }

    /**
     * @param mixed $str
     * @return mixed
     */
    private function prettyPlace($str)
    {
        $str = explode(', ', $str);

        return $str[0];
    }
}
