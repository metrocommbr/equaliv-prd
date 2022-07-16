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



namespace Mirasvit\ReportBuilder\Api\Repository;

use Mirasvit\ReportBuilder\Api\Data\ReportInterface;

interface ReportRepositoryInterface
{
    /**
     * @return \Mirasvit\ReportBuilder\Model\ResourceModel\Report\Collection|ReportInterface[]
     */
    public function getCollection();

    /**
     * @return ReportInterface
     */
    public function create();

    /**
     * @param int $id
     * @return ReportInterface|false
     */
    public function get($id);

    /**
     * @param ReportInterface $report
     * @return $this
     */
    public function save(ReportInterface $report);

    /**
     * @param ReportInterface $report
     * @return $this
     */
    public function delete(ReportInterface $report);

    /**
     * @return int
     */
    public function getUserId();
}
