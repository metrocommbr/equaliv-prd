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



namespace Mirasvit\Reports\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mirasvit\Reports\Helper\Geo as GeoHelper;

class Postcode extends AbstractDb
{
    /**
     * @var GeoHelper
     */
    protected $geoHelper;

    /**
     * @param GeoHelper $geoHelper
     * @param Context   $context
     */
    public function __construct(
        GeoHelper $geoHelper,
        Context $context
    ) {
        $this->geoHelper = $geoHelper;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('mst_reports_postcode', 'postcode_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $object)
    {
        /** @var \Mirasvit\Reports\Model\Postcode $object */
        $object->setPostcode($this->geoHelper->formatPostcode($object->getPostcode()));

        $names = ['state', 'province', 'place', 'community'];
        foreach ($names as $name) {
            $object->setData($name, $this->geoHelper->formatName($object->getData($name)));
        }

        if ($object->getUpdated()) {
            //            $this->geoHelper->synchronize($object);
        }

        return parent::_beforeSave($object);
    }
}
