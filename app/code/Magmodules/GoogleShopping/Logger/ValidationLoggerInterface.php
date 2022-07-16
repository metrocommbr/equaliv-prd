<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Logger;

/**
 * Interface ValidationLoggerInterface
 *
 * @package Magmodules\GoogleShopping\Logger
 */
interface ValidationLoggerInterface
{

    /**
     * @param string $type
     * @param $data
     * @return void
     */
    public function add($type, $data);
}
