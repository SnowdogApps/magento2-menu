<?php

declare(strict_types=1);

namespace Snowdog\Menu\Ui\Component\Listing\Column\MenuList;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

/**
 * Class PageActions
 *
 * Adds action buttons into actions column
 */
class PageActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'snowmenu/menu/edit';
    const URL_PATH_DELETE = 'snowmenu/menu/delete';
    const URL_PATH_STATUS = 'snowmenu/menu/status';
    const URL_PATH_DUPLICATE = 'snowmenu/menu/duplicate';
    const URL_PATH_EXPORT = 'snowmenu/menu/export';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['menu_id'])) {
                    $name = $this->getData('name');
                    $menuId = (int) $item['menu_id'];
                    $isActive = (int) $item['is_active'] === 1;
                    $item[$name] = [
                        'edit' => $this->getEditButton($menuId),
                        'delete' => $this->getDeleteButton($menuId),
                        'change_status' => $this->getChangeStatusButton($menuId, $isActive),
                        'duplicate' => $this->getDuplicateButton($menuId),
                        'export' => $this->getExportButton($menuId)
                    ];
                }
            }
        }

        return $dataSource;
    }

    private function getEditButton(int $menuId): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                static::URL_PATH_EDIT,
                [
                    'menu_id' => $menuId,
                ]
            ),
            'label' => __('Edit'),
            '__disableTmpl' => true,
        ];
    }

    private function getDeleteButton(int $menuId): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                static::URL_PATH_DELETE,
                [
                    'menu_id' => $menuId,
                ]
            ),
            'label' => __('Delete'),
            'confirm' => [
                'title' => __('Delete'),
                'message' => __('Are you sure you want to delete this menu?'),
            ],
            'post' => true,
            '__disableTmpl' => true,
        ];
    }

    private function getChangeStatusButton(int $menuId, bool $isActive): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                static::URL_PATH_STATUS,
                [
                    'menu_id' => $menuId,
                    'is_active' => !$isActive,
                ]
            ),
            'label' => __($isActive ? 'Disable' : 'Enable'),
            '__disableTmpl' => true,
        ];
    }

    private function getDuplicateButton(int $menuId): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                self::URL_PATH_DUPLICATE,
                [
                    'menu_id' => $menuId
                ]
            ),
            'label' => __('Duplicate'),
            '__disableTmpl' => true
        ];
    }

    private function getExportButton(int $menuId): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                self::URL_PATH_EXPORT,
                [
                    'menu_id' => $menuId
                ]
            ),
            'label' => __('Export'),
            '__disableTmpl' => true
        ];
    }
}
