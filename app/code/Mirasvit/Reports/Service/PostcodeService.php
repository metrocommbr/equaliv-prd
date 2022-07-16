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



namespace Mirasvit\Reports\Service;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;

class PostcodeService
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * PostcodeService constructor.
     *
     * @param ResourceConnection $resource
     * @param Context            $context
     */
    public function __construct(
        ResourceConnection $resource,
        Context $context
    ) {
        $this->resource       = $resource;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @param array|string $excluded
     * @return $this
     */
    public function delete($excluded)
    {
        try {
            $tableName  = $this->resource->getTableName('mst_reports_postcode');
            $connection = $this->resource->getConnection();

            if ('false' === $excluded) {
                $connection->query("DELETE from " . $tableName);
            }

            if (is_array($excluded)) {
                $excluded = "(" . implode(',', $excluded) . ")";
                $connection->query("DELETE from " . $tableName . " WHERE postcode_id NOT IN " . $excluded);
            }

            $this->messageManager->addSuccessMessage(
                __('Record(s) were removed from the postcode table.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this;
    }

    /**
     * @param mixed $country
     * @param mixed $postcode
     * @return array
     */
    public function findLocation($country, $postcode)
    {
        $location = [
            'state'     => false,
            'province'  => false,
            'place'     => false,
            'community' => false,
            'lat'       => false,
            'lng'       => false,
        ];

        $url = 'http://api.zippopotam.us/' . $country . '/' . $postcode;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);

        if (isset($response['places']) && isset($response['places'][0])) {
            $location = [
                'state'     => $response['places'][0]['state'],
                'province'  => false,
                'place'     => $response['places'][0]['place name'],
                'community' => false,
                'lat'       => $response['places'][0]['latitude'],
                'lng'       => $response['places'][0]['longitude'],
            ];
        }

        return $location;
    }

    /**
     * @param array $locations
     *
     * @return array
     */
    public function findInMapQuestApi(array $locations)
    {
        if (count($locations) === 0) {
            return [];
        }

        $result = [];

        $get = [];
        foreach ($locations as $id => $location) {
            $result[$id] = [];
            $get[]       = 'location=' . $location;
        }

        $get = implode('&', $get);
        $url = 'http://www.mapquestapi.com/geocoding/v1/batch?key=Kmjtd|luua2qu7n9,7a=o5-lzbgq&'
            . $get . '&outFormat=json';
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.mapquestapi.com/geocoding/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);

        $keys = array_keys($locations);

        if (isset($response['results'])) {
            foreach ($response['results'] as $idx => $locations) {
                $id = $keys[$idx];

                foreach ($locations['locations'] as $location) {
                    if ($location['postalCode']) {
                        $result[$id][] = $location;
                    }
                }
            }
        }

        return $result;
    }
}
