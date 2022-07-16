<?php
namespace Smartwave\Porto\Model\Config\Settings\Header;

class Toggle implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('On Click')],
            ['value' => '2', 'label' => __('On MouseOver')]
        ];
    }

    public function toArray()
    {
        return [
            '1' => __('On Click'),
            '2' => __('On MouseOver')
        ];
    }
}
