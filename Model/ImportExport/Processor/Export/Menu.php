<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Export;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Store;

class Menu
{
    const EXCLUDED_FIELDS = [
        MenuInterface::MENU_ID
    ];

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var Store
     */
    private $store;

    public function __construct(MenuRepositoryInterface $menuRepository, Store $store)
    {
        $this->menuRepository = $menuRepository;
        $this->store = $store;
    }

    /**
     * @param int $menuId
     * @return array
     */
    public function getData($menuId)
    {
        $menu = $this->menuRepository->getById($menuId); // Will throw an exception if menu is not found.
        $data = $menu->getData();

        foreach (self::EXCLUDED_FIELDS as $excludedField) {
            unset($data[$excludedField]);
        }

        $data[ExtendedFields::STORES] = $this->getStoreCodes($menu->getStores());

        return $data;
    }

    /**
     * @return array
     */
    private function getStoreCodes(array $stores)
    {
        $storeCodes = [];

        foreach ($stores as $storeId) {
            $storeCodes[] = $this->store->get($storeId)->getCode();
        }

        return $storeCodes;
    }
}
