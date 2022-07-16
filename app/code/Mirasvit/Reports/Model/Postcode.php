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



namespace Mirasvit\Reports\Model;

use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Reports\Helper\Geo as GeoHelper;

/**
 * @method string getCountryId()
 * @method $this setCountryId($countryCode)
 * @method string getPostcode()
 * @method $this setPostcode($postcode)
 * @method string getPlace()
 * @method $this setPlace($place)
 * @method string getState()
 * @method $this setState($state)
 * @method string getProvince()
 * @method $this setProvince($province)
 * @method string getCommunity()
 * @method $this setCommunity($community)
 * @method float getLat()
 * @method $this setLat($lat)
 * @method float getLng()
 * @method $this setLng($lng)
 * @method bool getUpdated()
 * @method $this setUpdated($updated)
 * @method string getOriginal()
 * @method $this setOriginal($data)
 */
class Postcode extends AbstractModel
{
    /**
     * @var PostcodeFactory
     */
    protected $postcodeFactory;

    /**
     * @var GeoHelper
     */
    protected $geoHelper;

    /**
     * Postcode constructor.
     * @param PostcodeFactory $postcodeFactory
     * @param GeoHelper $geoHelper
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        PostcodeFactory $postcodeFactory,
        GeoHelper $geoHelper,
        Context $context,
        Registry $registry
    ) {
        $this->postcodeFactory = $postcodeFactory;
        $this->geoHelper       = $geoHelper;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Reports\Model\ResourceModel\Postcode');
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getCountryId() . ': ' . $this->getPostcode()
            . ' / ' . $this->getState()
            . ' / ' . $this->getProvince()
            . ' / ' . $this->getPlace()
            . ' / ' . $this->getCommunity();
    }

    /**
     * @param string $countryId
     * @param string $postcode
     * @return bool|Postcode
     */
    public function loadByCode($countryId, $postcode)
    {
        $postcode = $this->geoHelper->formatPostcode($postcode);

        /** @var Postcode $model */
        $model = $this->getCollection()
            ->addFieldToFilter('country_id', $countryId)
            ->addFieldToFilter('postcode', $postcode)
            ->getFirstItem();

        if ($model->getId()) {
            return $model;
        }

        return false;
    }

    /**
     * @param string $fileUrl
     * @return bool
     * @throws \Exception
     */
    public function importFile($fileUrl)
    {
        $content      = file_get_contents($fileUrl);
        $tempFilePath = tempnam("", "geo");
        file_put_contents($tempFilePath, $content);

        $connection = $this->getResource()->getConnection();

        $file = new File();
        $csv  = new Csv($file);
        $data = $csv->getData($tempFilePath);

        $rows = [];
        $keys = [];
        foreach ($data as $item) {
            if (count($item) != 8) {
                continue;
            }

            $row = [
                'country_id' => $item[0],
                'postcode'   => $item[1],
                'place'      => $item[2],
                'state'      => $item[3],
                'province'   => $item[4],
                'community'  => $item[5],
                'lat'        => $item[6],
                'lng'        => $item[7],
                'updated'    => 1,
            ];

            $rows[] = $row;
            $keys[] = $row['country_id'] . $row['postcode'];

            if (count($rows) > 100) {
                $deleteCondition = [$connection->quoteInto('CONCAT_WS("", country_id, postcode) IN (?)', $keys)];

                // @todo: very slow query
                $connection->delete($this->getResource()->getTable('mst_reports_postcode'), $deleteCondition);

                $connection->insertMultiple(
                    $this->getResource()->getTable('mst_reports_postcode'),
                    $rows
                );

                $rows = [];
                $keys = [];
            }
        }

        if (count($rows)) {
            $deleteCondition = [$connection->quoteInto('CONCAT_WS("", country_id, postcode) IN (?)', $keys)];

            $connection->delete($this->getResource()->getTable('mst_reports_postcode'), $deleteCondition);

            $connection->insertMultiple(
                $this->getResource()->getTable('mst_reports_postcode'),
                $rows
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfUnknown()
    {
        //@todo EE multi db
        $connection = $this->getResource()->getConnection();

        $select = $connection->select()->from(
            ['address' => $this->getResource()->getTable('sales_order_address')],
            ['COUNT(*)']
        )->joinLeft(
            ['postcode' => $this->getResource()->getTable('mst_reports_postcode')],
            'postcode.postcode = REPLACE(REPLACE(address.postcode, " ", ""), "-","")
                AND postcode.country_id = address.country_id',
            []
        )->where(
            'postcode_id IS NULL or postcode.updated=0'
        )->where(
            'address.postcode IS NOT NULL'
        )->limit(1);

        return intval($connection->fetchOne($select));
    }
}
