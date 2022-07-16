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
 * @package   mirasvit/module-report-builder
 * @version   1.1.2
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportBuilder\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\ReportBuilder\Api\Data\ReportInterface;

class Report extends AbstractModel implements ReportInterface
{
    /**
     * @return mixed|string
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $value
     *
     * @return ReportInterface|Report
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @return int|mixed
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @param int $value
     *
     * @return ReportInterface|Report
     */
    public function setUserId($value)
    {
        return $this->setData(self::USER_ID, $value);
    }

    /**
     * @return mixed|string
     */
    public function getTable()
    {
        if ($this->getDimensions()) {
            list($table,) = explode('|', $this->getDimensions()[0]);
        } else {
            return 'sales_order';
        }

        return $table;
    }

    /** STATE
     * @return mixed|null
     */

    public function getColumns()
    {
        return $this->getConfigValue(self::COLUMNS, []);
    }

    /**
     * @param array $value
     *
     * @return ReportInterface|Report
     */
    public function setColumns(array $value)
    {
        return $this->setConfigValue(self::COLUMNS, $value);
    }

    /**
     * @return array|mixed|string[]|null
     */
    public function getDimensions()
    {
        $value = $this->getConfigValue(self::DIMENSIONS, []);

        return is_array($value) ? $value : [$value];
    }

    /**
     * @param array $value
     *
     * @return ReportInterface|Report
     */
    public function setDimensions(array $value)
    {
        return $this->setConfigValue(self::DIMENSIONS, $value);
    }

    /**
     * @return mixed|string[]|null
     */
    public function getInternalColumns()
    {
        return $this->getConfigValue(self::INTERNAL_COLUMNS, []);
    }

    /**
     * @param array $value
     *
     * @return ReportInterface|Report
     */
    public function setInternalColumns(array $value)
    {
        return $this->setConfigValue(self::INTERNAL_COLUMNS, $value);
    }

    /**
     * @return array|mixed|null
     */
    public function getInternalFilters()
    {
        return $this->getConfigValue(self::INTERNAL_FILTERS, []);
    }

    /**
     * @param array $value
     *
     * @return ReportInterface|Report
     */
    public function setInternalFilters(array $value)
    {
        return $this->setConfigValue(self::INTERNAL_FILTERS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->getConfigValue(self::FILTERS);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilters(array $filters)
    {
        return $this->setConfigValue(self::FILTERS, array_values($filters));
    }

    /** SCHEMA
     * @return mixed|null
     */

    public function getPrimaryDimensions()
    {
        return $this->getConfigValue(self::PRIMARY_DIMENSIONS, []);
    }

    /**
     * @param array $value
     *
     * @return ReportInterface|Report
     */
    public function setPrimaryDimensions(array $value)
    {
        return $this->setConfigValue(self::PRIMARY_DIMENSIONS, $value);
    }

    /**
     * @return mixed|string[]|null
     */
    public function getPrimaryFilters()
    {
        return $this->getConfigValue(self::PRIMARY_FILTERS, []);
    }

    /**
     * @param array $value
     *
     * @return ReportInterface|Report
     */
    public function setPrimaryFilters(array $value)
    {
        return $this->setConfigValue(self::PRIMARY_FILTERS, $value);
    }

    /**
     * @return \Mirasvit\Report\Model\GridConfig|void
     */
    public function getGridConfig()
    {
        // TODO: Implement getGridConfig() method.
    }

    ////

    /**
     * @return \Mirasvit\Report\Model\ChartConfig|void
     */
    public function getChartConfig()
    {
        // TODO: Implement getChartConfig() method.
    }

    /**
     * @param string $tableName
     *
     * @return ReportInterface|void
     */
    public function setTable($tableName)
    {
        // TODO: Implement setTable() method.
    }

    /**
     * @return ReportInterface|void
     */
    public function init()
    {
        // TODO: Implement init() method.
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Report::class);
    }

    /**
     * @return array|mixed
     * @throws \Zend_Json_Exception
     */
    private function getConfig()
    {
        $config = $this->getData(self::CONFIG);

        $config = $config ? \Zend_Json::decode($config, \Zend_Json::TYPE_ARRAY) : [];

        return $config;
    }

    /**
     * @param string $value
     *
     * @return Report
     */
    private function setConfig($value)
    {
        return $this->setData(self::CONFIG, \Zend_Json::encode($value));
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    private function getConfigValue($key, $default = null)
    {
        $config = $this->getConfig();

        return isset($config[$key]) ? $config[$key] : $default;
    }

    /**
     * @param string       $key
     * @param string|array $value
     *
     * @return Report
     */
    private function setConfigValue($key, $value)
    {
        $config       = $this->getConfig();
        $config[$key] = $value;

        return $this->setConfig($config);
    }
}
