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



namespace Mirasvit\ReportBuilder\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\ReportBuilder\Api\Data\ReportInterface;
use Mirasvit\ReportBuilder\Api\Repository\ReportRepositoryInterface;
use Mirasvit\ReportBuilder\Api\Data\ReportInterfaceFactory;
use Mirasvit\ReportBuilder\Model\ResourceModel\Report\CollectionFactory;
use Magento\Backend\Model\Auth\Session;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ReportInterfaceFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * ReportRepository constructor.
     * @param EntityManager $entityManager
     * @param ReportInterfaceFactory $factory
     * @param CollectionFactory $collectionFactory
     * @param Session $authSession
     */
    public function __construct(
        EntityManager $entityManager,
        ReportInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        Session $authSession
    ) {
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->authSession = $authSession;
    }

    /**
     * @return ReportInterface[]|\Mirasvit\ReportBuilder\Model\ResourceModel\Report\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return ReportInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $model = $this->create();
        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : false;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ReportInterface $report)
    {
        return $this->entityManager->save($report);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ReportInterface $report)
    {
        $this->entityManager->delete($report);

        return $this;
    }

    /**
     * @return int|mixed
     */
    public function getUserId()
    {
        return $this->authSession->getUser() ? $this->authSession->getUser()->getId() : 0;
    }
}
