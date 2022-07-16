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



namespace Mirasvit\Reports\Model;

use Magento\Framework\App\RequestInterface;
use Mirasvit\Report\Api\Data\ReportInterface;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;

class Manager
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    /**
     * Manager constructor.
     * @param RequestInterface $request
     * @param ReportRepositoryInterface $reportRepository
     */
    public function __construct(
        RequestInterface $request,
        ReportRepositoryInterface $reportRepository
    ) {
        $this->request          = $request;
        $this->reportRepository = $reportRepository;
    }

    /**
     * @param string $code
     * @return ReportInterface
     */
    public function getReport($code = null)
    {
        if (!$code) {
            $code = $this->request->getParam('report');
        }

        if (!$code) {
            if ($this->reportRepository->get('order_overview')) {
                $code = 'order_overview';
            } else {
                $code = $this->reportRepository->getList()[0]->getIdentifier();
            }
        }

        return $this->reportRepository->get($code);
    }
}
