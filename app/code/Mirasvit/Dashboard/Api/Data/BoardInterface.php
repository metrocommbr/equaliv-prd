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



namespace Mirasvit\Dashboard\Api\Data;

interface BoardInterface
{
    const TABLE_NAME = 'mst_dashboard_board';

    const TYPE_PRIVATE = 'private';
    const TYPE_SHARED  = 'shared';

    const ID                = 'board_id';
    const IDENTIFIER        = 'identifier';
    const TITLE             = 'title';
    const TYPE              = 'type';
    const IS_DEFAULT        = 'is_default';
    const USER_ID           = 'user_id';
    const BLOCKS            = 'blocks';
    const BLOCKS_SERIALIZED = 'blocks_serialized';
    const IS_MOBILE_ENABLED = 'is_mobile_enabled';
    const MOBILE_TOKEN      = 'mobile_token';
    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $value
     * @return $this
     */
    public function setIdentifier($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $data
     * @return $this
     */
    public function setType($data);

    /**
     * @return bool
     */
    public function isDefault();

    /**
     * @param bool $data
     * @return $this
     */
    public function setIsDefault($data);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param int $data
     * @return $this
     */
    public function setUserId($data);

    /**
     * @return bool
     */
    public function isMobileEnable();

    /**
     * @param bool $input
     * @return $this
     */
    public function setIsMobileEnabled($input);

    /**
     * @return string
     */
    public function getMobileToken();

    /**
     * @param string $input
     * @return $this
     */
    public function setMobileToken($input);

    /**
     * @return BlockInterface[]
     */
    public function getBlocks();

    /**
     * @param BlockInterface[] $value
     * @return $this
     */
    public function setBlocks($value);

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}