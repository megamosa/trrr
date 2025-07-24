<?php
namespace MagoArab\OrderTracking\Model\Config\Source;

class MobileFormats implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'digits_only', 'label' => __('Digits Only (1234567890)')],
            ['value' => 'international', 'label' => __('International Format (+1234567890)')],
            ['value' => 'local', 'label' => __('Local Format (123-456-7890)')],
            ['value' => 'spaces', 'label' => __('With Spaces (123 456 7890)')],
            ['value' => 'brackets', 'label' => __('With Brackets ((123) 456-7890)')]
        ];
    }
}