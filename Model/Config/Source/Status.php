<?php

namespace Snowdog\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['label' => __('Disabled'), 'value' => '0'],
            ['label' => __('Enabled'), 'value' => '1'],
        ];
    }
}
