<?php

namespace Metrocomm\Prescriptor\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Prescriptor extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'metrocomm_prescriptor_entity',
            'entity_id'
        );
    }
}
