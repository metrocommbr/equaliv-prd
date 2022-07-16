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



namespace Mirasvit\Dashboard\Model;

use Magento\Framework\DataObject;
use Mirasvit\Dashboard\Api\Data\BlockInterface;
use Mirasvit\Dashboard\Model\Block\Config;

class Block extends DataObject implements BlockInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        $value = $this->getData(BlockInterface::IDENTIFIER);

        return $value ? $value : hash('sha256', rand(1, microtime(true)));
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($value)
    {
        return $this->setData(BlockInterface::IDENTIFIER, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getPos()
    {
        return $this->getData(BlockInterface::POS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPos($data)
    {
        return $this->setData(BlockInterface::POS, [(int)$data[0], (int)$data[1]]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->getData(BlockInterface::SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($data)
    {
        return $this->setData(BlockInterface::SIZE, [(int)$data[0], (int)$data[1]]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(BlockInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($value)
    {
        return $this->setData(BlockInterface::TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(BlockInterface::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($value)
    {
        return $this->setData(BlockInterface::DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $value = $this->getData(BlockInterface::CONFIG);
        if ($value === null) {
            $value = [];
        }

        return new Config($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($value)
    {
        return $this->setData(BlockInterface::CONFIG, $value);
    }
}