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



namespace Mirasvit\Dashboard\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Dashboard\Api\Data\BoardInterface;
use Mirasvit\Dashboard\Api\Repository\BoardRepositoryInterface;
use Mirasvit\Dashboard\Model\BoardFactory;
use Mirasvit\Dashboard\Model\ResourceModel\Board\CollectionFactory as BoardCollectionFactory;

class BoardRepository implements BoardRepositoryInterface
{
    /**
     * @var BoardFactory
     */
    private $boardFactory;

    /**
     * @var BoardCollectionFactory
     */
    private $boardCollectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * BoardRepository constructor.
     * @param BoardFactory $boardFactory
     * @param BoardCollectionFactory $boardCollectionFactory
     * @param EntityManager $entityManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        BoardFactory $boardFactory,
        BoardCollectionFactory $boardCollectionFactory,
        EntityManager $entityManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->boardFactory           = $boardFactory;
        $this->boardCollectionFactory = $boardCollectionFactory;
        $this->entityManager          = $entityManager;
        $this->objectManager          = $objectManager;
    }

    /**
     * @return \Mirasvit\Dashboard\Model\ResourceModel\Board\Collection|BoardInterface[]
     */
    public function getCollection()
    {
        return $this->boardCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($boardId)
    {
        $board = $this->create();

        $this->entityManager->load($board, $boardId);

        if (!$board->getId()) {
            return false;
        }

        return $board;
    }

    /**
     * @param string $identifier
     * @return bool|BoardInterface|\Mirasvit\Dashboard\Model\Board
     */
    public function getByIdentifier($identifier)
    {
        $board = $this->create();

        $board->load($identifier, BoardInterface::IDENTIFIER);

        if (!$board->getId()) {
            return false;
        }

        return $board;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->boardFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BoardInterface $board)
    {
        $this->entityManager->delete($board);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save(BoardInterface $board)
    {
        // This prevents error "Area code is not set", when we create board during installation
        if ($board->getType() == BoardInterface::TYPE_PRIVATE && !$board->getUserId()) {
            /** @var \Magento\Backend\Model\Auth\Session $session */
            $session = $this->objectManager->get('Magento\Backend\Model\Auth\Session');
            if ($session->getUser()) {
                $board->setUserId($session->getUser()->getId());
            }
        }

        if (!$board->getMobileToken()) {
            $board->setMobileToken(hash('sha256', microtime(true)));
        }

        if (!$board->getIdentifier()) {
            $board->setIdentifier(hash('sha256', microtime(true)));
        }

        if (!$board->getType()) {
            $board->setType(BoardInterface::TYPE_SHARED);
        }

        $this->entityManager->save($board);

        return $board;
    }
}
