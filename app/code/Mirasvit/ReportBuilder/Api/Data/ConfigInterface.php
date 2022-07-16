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



namespace Mirasvit\ReportBuilder\Api\Data;

interface ConfigInterface
{
    const TABLE_NAME = 'mst_report_builder_config';

    const ID = 'config_id';
    const TITLE = 'title';
    const CONFIG = 'config';
    const USER_ID = 'user_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $value
     * @return $this
     */
    public function setConfig($value);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param int $value
     * @return $this
     */
    public function setUserId($value);
}
