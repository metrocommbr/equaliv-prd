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



namespace Mirasvit\Dashboard\Service;

use Magento\Backend\Model\Auth\Session;
use Mirasvit\Dashboard\Api\Data\BoardInterface;
use Mirasvit\Dashboard\Api\Repository\BoardRepositoryInterface;

class BoardService
{
    /**
     * @var BoardRepositoryInterface
     */
    private $boardRepository;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * BoardService constructor.
     * @param BoardRepositoryInterface $boardRepository
     * @param Session $authSession
     */
    public function __construct(
        BoardRepositoryInterface $boardRepository,
        Session $authSession
    ) {
        $this->boardRepository = $boardRepository;
        $this->authSession     = $authSession;
    }

    /**
     * @return BoardInterface[]
     */
    public function getAllowedBoards()
    {
        $userId = $this->authSession->getUser() ? $this->authSession->getUser()->getId() : 0;

        return $this->boardRepository->getCollection()
            ->addFieldToFilter(
                [BoardInterface::TYPE, BoardInterface::USER_ID],
                [BoardInterface::TYPE_SHARED, $userId]
            );
    }
}
