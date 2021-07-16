<?php

declare(strict_types=1);

namespace Snowdog\Menu\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ImportCategories extends AbstractButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Import from Categories'),
            'class' => 'import',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'snowmenu_menu_form.snowmenu_menu_form.import_categories_tree_modal',
                                'actionName' => 'toggleModal'
                            ]
                        ]
                    ]
                ]
            ],
            'on_click' => '',
            'sort_order' => 45
        ];
    }
}
