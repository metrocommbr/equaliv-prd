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


namespace Mirasvit\Dashboard\Api\Repository;

use Mirasvit\Dashboard\Api\Data\BoardInterface;

interface BoardRepositoryInterface
{
    /**
     * @return \Mirasvit\Dashboard\Model\ResourceModel\Board\Collection|BoardInterface[]
     */
    public function getCollection();

    /**
     * @param BoardInterface $board
     * @return BoardInterface
     */
    public function save(BoardInterface $board);

    /**
     * @param int $boardId
     * @return BoardInterface|false
     */
    public function get($boardId);

    /**
     * @return BoardInterface
     */
    public function create();

    /**
     * @param BoardInterface $board
     * @return bool
     */
    public function delete(BoardInterface $board);
}