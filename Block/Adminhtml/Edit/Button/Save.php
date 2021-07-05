<?php

namespace Snowdog\Menu\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

class Save extends AbstractButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $data = [
            'class' => 'save primary',
            'sort_order' => 50,
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getButtonOptionsList()
        ];

        return array_merge($this->getData('Save', 'continue'), $data);
    }

    private function getButtonOptionsList(): array
    {
        return [
            $this->getData('Save & Duplicate', 'duplicate', 'save_and_duplicate'),
            $this->getData('Save & Close', 'close', 'save_and_close')
        ];
    }

    private function getData(string $label, string $backParam, ?string $idHard = null): array
    {
        $data = [
            'label' => __($label),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'snowmenu_menu_form.snowmenu_menu_form',
                                'actionName' => 'save',
                                'params' => [true, ['back' => $backParam]]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($idHard !== null) {
            $data['id_hard'] = $idHard;
        }

        return $data;
    }
}
