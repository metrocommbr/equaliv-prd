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

use Magento\Framework\Model\AbstractModel;
use Mirasvit\Dashboard\Api\Data\BlockInterface;
use Mirasvit\Dashboard\Api\Data\BoardInterface;

class Board extends AbstractModel implements BoardInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Board::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getData(BoardInterface::IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($value)
    {
        return $this->setData(BoardInterface::IDENTIFIER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(BoardInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(BoardInterface::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(BoardInterface::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($data)
    {
        return $this->setData(BoardInterface::TYPE, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return $this->getData(BoardInterface::IS_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDefault($data)
    {
        return $this->setData(BoardInterface::IS_DEFAULT, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->getData(BoardInterface::USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($data)
    {
        return $this->setData(BoardInterface::USER_ID, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function isMobileEnable()
    {
        return $this->getData(BoardInterface::IS_MOBILE_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsMobileEnabled($input)
    {
        return $this->setData(BoardInterface::IS_MOBILE_ENABLED, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function getMobileToken()
    {
        return $this->getData(BoardInterface::MOBILE_TOKEN);
    }

    /**
     * {@inheritdoc}
     */
    public function setMobileToken($input)
    {
        return $this->setData(BoardInterface::MOBILE_TOKEN, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        $blocks = [];
        try {
            $data = \Zend_Json::decode($this->getData(BoardInterface::BLOCKS_SERIALIZED));
            if ($data === null) {
                $data = [];
            }

            foreach ($data as $item) {
                $blocks[] = new Block($item);
            }
        } catch (\Exception $e) {
            $blocks = [];
        }

        return $blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function setBlocks($blocks)
    {
        $data = [];

        foreach ($blocks as $item) {
            $data[] = [
                BlockInterface::IDENTIFIER  => $item->getIdentifier(),
                BlockInterface::TITLE       => $item->getTitle(),
                BlockInterface::SIZE        => $item->getSize(),
                BlockInterface::POS         => $item->getPos(),
                BlockInterface::DESCRIPTION => $item->getDescription(),
                BlockInterface::CONFIG      => $item->getConfig()->getData(),
            ];
        }

        return $this->setData(BoardInterface::BLOCKS_SERIALIZED, \Zend_Json::encode($data));
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);

        return $this;
    }
}