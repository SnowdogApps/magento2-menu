<?php

namespace Snowdog\Menu\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Reset extends AbstractButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Reset'),
            'on_click' => 'window.location.reload();',
            'class' => 'reset',
            'sort_order' => 30
        ];
    }
}
