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



namespace Mirasvit\ReportBuilder\Model;

use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Context;
use Mirasvit\ReportBuilder\Api\Data\ReportInterface;
use Mirasvit\ReportBuilder\Api\Repository\ReportRepositoryInterface;

class ReportInstance extends AbstractReport
{
    const IDENTIFIER = 'identifier';

    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ReportInterface
     */
    private $report;

    /**
     * ReportInstance constructor.
     * @param ReportRepositoryInterface $reportRepository
     * @param Context $context
     */
    public function __construct(
        ReportRepositoryInterface $reportRepository,
        Context $context
    ) {
        $this->reportRepository = $reportRepository;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * @param ReportInterface $report
     * @return $this
     */
    public function setReport(ReportInterface $report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->_get(self::IDENTIFIER);
    }

    /**
     * @param string $identifier
     * @return ReportInstance
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $report = $this->reportRepository->get($this->getIdentifier());

        $this->setTable($report->getTable())
            ->setColumns($report->getColumns())
            ->setDimensions($report->getDimensions())
            ->setInternalColumns($report->getInternalColumns())
            ->setInternalFilters($report->getInternalFilters());

        $this->setPrimaryDimensions($report->getPrimaryDimensions())
            ->setPrimaryFilters($report->getPrimaryFilters());

        if ($report->getChartType() && $report->getChartType() !== 'none') {
            $this->getChartConfig()
                ->setType($report->getChartType())
                ->setDefaultColumns($report->getChartColumns());
        }


        //            ->setDimensions(
        //            $this->normalize($config['dimensions'], true)
        //        )->setFastFilters(
        //            $this->normalize($config['fast_filters'], true)
        //        )->addAvailableFilters(
        //            $this->normalize($config['available_filters'], true)
        //        )->setColumns([]);
        //
        //        $this->getChartConfig()
        //            ->setType($this->normalize($config['chart_type']))
        //            ->setDefaultColumns($this->normalize($config['chart_columns'], true));

        return $this;
    }
}
