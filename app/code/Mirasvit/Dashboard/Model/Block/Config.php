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
 * @package   mirasvit/module-dashboard
 * @version   1.3.2
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Dashboard\Model\Block;

use Magento\Framework\DataObject;

class Config extends DataObject
{
    /**
     * @return mixed|string
     */
    public function getRenderer()
    {
        $value = $this->getData('renderer');

        return $value ? $value : 'single';
    }

    /**
     * @return Single
     */
    public function getSingle()
    {
        $value = $this->getData('single');

        return new Single($value ? $value : []);
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        $value = $this->getData('table');

        return new Table($value);
    }

    /**
     * @return Chart
     */
    public function getChart()
    {
        $value = $this->getData('chart');

        return new Chart($value ? $value : []);
    }

    /**
     * @return array|mixed
     */
    public function getFilters()
    {
        $value = $this->getData('filters');

        return is_array($value) ? $value : [];
    }

    /**
     * @return DateRange
     */
    public function getDateRange()
    {
        $value = $this->getData('date_range');

        return new DateRange($value ? $value : []);
    }
}
