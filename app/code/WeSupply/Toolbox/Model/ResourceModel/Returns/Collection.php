<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Model\ResourceModel\Returns;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package WeSupply\Toolbox\Model\ResourceModel\Returns
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'WeSupply\Toolbox\Model\Returns',
            'WeSupply\Toolbox\Model\ResourceModel\Returns'
        );
    }
}
