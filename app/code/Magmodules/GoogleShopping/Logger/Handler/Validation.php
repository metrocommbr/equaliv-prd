<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Magento\Framework\Logger\Monolog;

/**
 * Class Logger Data
 */
class Validation extends Base
{

    /**
     * @var string
     */
    protected $fileName = '/var/log/googleshopping/validation.log';

    /**
     * @var int
     */
    protected $loggerType = Monolog::DEBUG;
}
