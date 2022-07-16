<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Logger;

/**
 * Interface GeneralLoggerInterface
 *
 * @package Magmodules\GoogleShopping\Logger
 */
interface GeneralLoggerInterface
{

    /**
     * @param string $type
     * @param $data
     * @return void
     */
    public function add($type, $data);
}
