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
 * @package   mirasvit/module-reports
 * @version   1.4.4
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Reports\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;

class Menu extends AbstractMenu
{
    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    /**
     * Menu constructor.
     * @param ReportRepositoryInterface $reportRepository
     * @param Context $context
     */
    public function __construct(
        ReportRepositoryInterface $reportRepository,
        Context $context
    ) {
        $this->visibleAt(['reports']);

        $this->reportRepository = $reportRepository;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        foreach ($this->reportRepository->getList() as $report) {
            $name = $report->getName();
            if (!$name) {
                continue;
            }

            $group = explode('_', $report->getIdentifier())[0];
            if (is_numeric($group)) {
                $group = 'custom';
            }

            if (isset($submenu) && $group !== $submenu) {
                $this->addSeparator();
            }
            $submenu = $group;

            $this->addItem([
                'id'       => $report->getIdentifier(),
                'resource' => 'Mirasvit_Reports::reports_view',
                'title'    => $name,
                'url'      => $this->urlBuilder->getUrl('reports/report/view', [
                    'report' => $report->getIdentifier(),
                ]),
            ]);
        }

        $this->addSeparator();

        $this->addItem([
            'id'       => 'email',
            'resource' => 'Mirasvit_Report::email',
            'title'    => __('Email Notifications'),
            'url'      => $this->urlBuilder->getUrl('report/email/index'),
        ])->addItem([
            'id'       => 'geo',
            'resource' => 'Mirasvit_Reports::reports_view',
            'title'    => __('Manage Geo data'),
            'url'      => $this->urlBuilder->getUrl('reports/geo/index'),
        ]);

        return $this;
    }
}
