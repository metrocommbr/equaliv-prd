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

interface ReportInterface extends \Mirasvit\Report\Api\Data\ReportInterface
{
    const TABLE_NAME = 'mst_report_builder_report';

    const ID      = 'report_id';
    const NAME    = 'title';
    const CONFIG  = 'config';
    const USER_ID = 'user_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param int $value
     * @return $this
     */
    public function setUserId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);
}
