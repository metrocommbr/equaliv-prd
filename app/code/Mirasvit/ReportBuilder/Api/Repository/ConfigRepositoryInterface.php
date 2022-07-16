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

use Mirasvit\ReportBuilder\Api\Data\ConfigInterface;

interface ConfigRepositoryInterface
{
    /**
     * @return \Mirasvit\ReportBuilder\Model\ResourceModel\Config\Collection|ConfigInterface[]
     */
    public function getCollection();

    /**
     * @return ConfigInterface
     */
    public function create();

    /**
     * @param int $id
     * @return ConfigInterface|false
     */
    public function get($id);

    /**
     * @param ConfigInterface $config
     * @return $this
     */
    public function save(ConfigInterface $config);

    /**
     * @param ConfigInterface $config
     * @return $this
     */
    public function delete(ConfigInterface $config);

    /**
     * @return int
     */
    public function getUserId();
}
