<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Magento\Framework\Logger\Monolog;

/**
 * Class General
 *
 * @package Magmodules\GoogleShopping\Logger\Handler
 */
class General extends Base
{

    /**
     * @var string
     */
    protected $fileName = '/var/log/googleshopping/general.log';

    /**
     * @var int
     */
    protected $loggerType = Monolog::DEBUG;
}
