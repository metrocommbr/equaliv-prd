<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Returns
 *
 * @package WeSupply\Toolbox\Model\ResourceModel
 */
class Returns extends AbstractDb
{
    /**
     * Returns constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('wesupply_returns_list', 'id');
    }
}
