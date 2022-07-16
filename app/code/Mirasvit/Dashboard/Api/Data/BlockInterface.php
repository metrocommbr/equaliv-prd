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

use Mirasvit\Dashboard\Model\Block\Config;

interface BlockInterface
{
    const IDENTIFIER  = 'identifier';
    const POS         = 'pos';
    const SIZE        = 'size';
    const TITLE       = 'title';
    const DESCRIPTION = 'description';
    const CONFIG      = 'config';

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
     * @return array
     */
    public function getPos();

    /**
     * @param array $pos
     * @return $this
     */
    public function setPos($pos);

    /**
     * @return array
     */
    public function getSize();

    /**
     * @param array $size
     * @return $this
     */
    public function setSize($size);

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
     * @return string
     */
    public function getDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setDescription($value);

    /**
     * @return Config
     */
    public function getConfig();

    /**
     * @param array $value
     * @return $this
     */
    public function setConfig($value);
}