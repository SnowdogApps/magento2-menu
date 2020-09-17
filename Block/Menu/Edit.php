<?php

namespace Snowdog\Menu\Block\Menu;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Ui\Component\Control\Container as ControlContainer;

class Edit extends FormContainer
{
    protected function _construct()
    {
        $this->_blockGroup = 'Snowdog_Menu';
        $this->_controller = 'menu';
        $this->_mode = 'edit';
        parent::_construct();

        $this->buttonList->remove('save');

        $this->buttonList->add(
            'save',
            [
                'label' => __('Save'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ],
                'class_name' => ControlContainer::SPLIT_BUTTON,
                'options' => $this->getSaveButtonOptions()
            ],
            -100
        );
    }

    /**
     * @return array
     */
    private function getSaveButtonOptions()
    {
        return [
            [
                'id_hard' => 'save_and_duplicate',
                'label' => __('Save & Duplicate'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form',
                            'eventData' => ['action' => ['args' => ['back' => 'duplicate']]],
                        ]
                    ]
                ]
            ],
            [
                'id_hard' => 'save_and_close',
                'label' => __('Save & Close'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'save',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ]
        ];
    }
}
