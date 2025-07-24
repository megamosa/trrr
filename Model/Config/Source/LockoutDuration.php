<?php
namespace MagoArab\OrderTracking\Model\Config\Source;

class LockoutDuration implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 5, 'label' => __('5 minutes')],
            ['value' => 10, 'label' => __('10 minutes')],
            ['value' => 15, 'label' => __('15 minutes')],
            ['value' => 30, 'label' => __('30 minutes')],
            ['value' => 60, 'label' => __('1 hour')],
            ['value' => 120, 'label' => __('2 hours')]
        ];
    }
}