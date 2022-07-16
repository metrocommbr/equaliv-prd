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



namespace Mirasvit\Dashboard\Repository\Email;

use Mirasvit\Dashboard\Api\Data\BlockInterface;
use Mirasvit\Dashboard\Repository\BoardRepository;
use Mirasvit\Dashboard\Service\BlockService;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Mirasvit\ReportApi\Api\ResponseInterface;

class BlockRepository implements \Mirasvit\Report\Api\Repository\Email\BlockRepositoryInterface
{
    /**
     * @var BoardRepository
     */
    private $boardRepository;

    /**
     * @var DateServiceInterface
     */
    private $dateService;

    /**
     * @var BlockService
     */
    private $blockService;

    /**
     * BlockRepository constructor.
     * @param BoardRepository $boardRepository
     * @param DateServiceInterface $dateService
     * @param BlockService $blockService
     */
    public function __construct(
        BoardRepository $boardRepository,
        DateServiceInterface $dateService,
        BlockService $blockService
    ) {
        $this->boardRepository = $boardRepository;
        $this->dateService     = $dateService;
        $this->blockService    = $blockService;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        $blocks = [];
        $boards = $this->boardRepository->getCollection();

        foreach ($boards as $board) {
            foreach ($board->getBlocks() as $block) {
                $index = implode(':', [
                    $board->getIdentifier(), $block->getIdentifier(),
                ]);

                $blocks[$index] = __('Dashboard: %1 - %2', $board->getTitle(), $block->getTitle());
            }
        }

        return $blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($identifier, $data)
    {
        list($boardIdentifier, $blockIdentifier) = explode(':', $identifier);

        $board = $this->boardRepository->getByIdentifier($boardIdentifier);
        if (!$board) {
            return null;
        }

        $block = null;
        foreach ($board->getBlocks() as $item) {
            if ($item->getIdentifier() === $blockIdentifier) {
                $block = $item;
                break;
            }
        }

        if (!$block) {
            return null;
        }
        $interval = $this->dateService->getInterval($data['timeRange']);

        $response = $this->blockService->getApiResponse($block, [
            [
                'column'         => 'DATE',
                'condition_type' => 'gteq',
                'value'          => $interval->getFrom()->toString(\Zend_Date::W3C),
            ],
            [
                'column'         => 'DATE',
                'condition_type' => 'lteq',
                'value'          => $interval->getTo()->toString(\Zend_Date::W3C),
            ],
        ]);


        if (!$response) {
            return null;
        }

        if ($block->getConfig()->getRenderer() === 'single') {
            return $this->renderSingle($block, $response, $data['timeRange']);
        } elseif ($block->getConfig()->getRenderer() === 'table') {
            return $this->renderTable($block, $response, $data['timeRange']);
        }

        return null;
    }
   
    /**
     * @param BlockInterface $block
     * @param ResponseInterface $response
     * @param ResponseInterface $timeRange
     *
     * @return string
     */
    private function renderTable(BlockInterface $block, ResponseInterface $response, $timeRange)
    {
        $rows = [];
        foreach ($response->getColumns() as $column) {
            $rows['header'][] = $column->getLabel();
        }


        foreach ($response->getItems() as $idx => $item) {
            foreach ($item->getFormattedData() as $value) {
                $rows[$idx][] = $value;
            }
        }

        foreach ($response->getTotals()->getFormattedData() as $value) {
            $rows['footer'][] = $value;
        }

        $table = '<table>';
        foreach ($rows as $idx => $row) {
            $table .= '<tr>';
            foreach ($row as $column) {
                if ($idx === 'header' || $idx === 'footer') {
                    $table .= '<th>' . $column . '</th>';
                } else {
                    $table .= '<td>' . $column . '</td>';
                }
            }
            $table .= '</tr>';
        }

        $table .= '</table>';

        $name = $block->getTitle();

        return "
            <h2>{$name}</h2>
             <div class='interval'>{$this->dateService->getIntervalHint($timeRange)}</div>
             <div class='table-wrapper'>$table</div>
        ";
    }

    /**
     * @param BlockInterface    $block
     * @param ResponseInterface $response
     * @param ResponseInterface $timeRange
     *
     * @return string
     */
    private function renderSingle(BlockInterface $block, ResponseInterface $response, $timeRange)
    {
        $identifier = $block->getConfig()->getSingle()->getColumn();

        $fValue        = $response->getTotals()->getFormattedData($identifier);
        $fValueCompare = $response->getTotals()->getFormattedData('C|' . $identifier);

        $value        = $response->getTotals()->getData($identifier);
        $valueCompare = $response->getTotals()->getData('C|' . $identifier);

        $compareHtml = '';
        if ($value && $valueCompare) {
            $percent = ($value / $valueCompare - 1) * 100;

            if ($percent > 0) {
                $compareHtml = "<span class='positive'>+" . round($percent, 1) . "%</span>";
            } else {
                $compareHtml = "<span class='negative'>" . round($percent, 1) . "%</span>";
            }
        }

        $html
            = "
            <h2>{$block->getTitle()}</h2>
            <div class='interval'>{$this->dateService->getIntervalHint($timeRange)}</div>
            <div class='value-wrapper'>$fValue</div>
            <div class='value-wrapper comparison'>$fValueCompare $compareHtml</div>
        ";

        return $html;
    }
}
