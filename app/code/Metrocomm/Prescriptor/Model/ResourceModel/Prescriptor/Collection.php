<?php

namespace Metrocomm\Prescriptor\Model\ResourceModel\Prescriptor;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Metrocomm\Prescriptor\Model\Prescriptor;
use Metrocomm\Prescriptor\Model\ResourceModel\Prescriptor as PrescriptorResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            Prescriptor::class,
            PrescriptorResourceModel::class
        );
    }
}
