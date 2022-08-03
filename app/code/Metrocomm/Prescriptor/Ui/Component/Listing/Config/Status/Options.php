<?php

namespace Metrocomm\Prescriptor\Ui\Component\Listing\Config\Status;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{

    protected $options;

    public function toOptionArray(): array
    {
        $this->options = [
            [
                'value' => 'pending',
                'label'=> 'Pending'
            ],
            [
                'value' => 'error',
                'label'=> 'Error'
            ],
            [
                'value' => 'approved',
                'label'=> 'Approved'
            ],
            [
                'value' => 'disapproved',
                'label'=> 'Disapproved'
            ]

        ];
        return $this->options;
    }
}

