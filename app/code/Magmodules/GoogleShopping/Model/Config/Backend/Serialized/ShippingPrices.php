<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Model\Config\Backend\Serialized;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

/**
 * Class ShippingPrices
 *
 * @package Magmodules\GoogleShopping\Model\Config\Backend\Serialized
 */
class ShippingPrices extends ArraySerialized
{

    /**
     * Reformat Shipping Prices and uset unused.
     *
     * @return \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
     */
    public function beforeSave()
    {
        $data = $this->getValue();
        if (is_array($data)) {
            foreach ($data as $key => $row) {
                if (empty($row['code'])) {
                    unset($data[$key]);
                    continue;
                }
                if (empty($row['price_from'])) {
                    $row['price_from'] = '0';
                }
                if (empty($row['price_to']) || $row['price_to'] < 1) {
                    $row['price_to'] = '999999';
                }
                if (empty($row['price'])) {
                    $row['price'] = '0';
                }
                $data[$key]['price_from'] = number_format(str_replace(',', '.', $row['price_from']), 2, '.', '');
                $data[$key]['price_to'] = number_format(str_replace(',', '.', $row['price_to']), 2, '.', '');
                $data[$key]['price'] = number_format(str_replace(',', '.', $row['price']), 2, '.', '');
            }
        }
        $this->setValue($data);
        return parent::beforeSave();
    }
}
