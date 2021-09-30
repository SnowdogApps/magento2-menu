<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Store\Model\Store;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;

class Menu
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var array
     */
    private $loadedMenus = [];

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        MenuRepositoryInterface $menuRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->menuRepository = $menuRepository;
    }

    public function getList(array $identifiers, int $storeId): array
    {
        $searchCriteria = $this->prepareSearchCriteriaBuilder($identifiers, $storeId)->create();
        $menuList = $this->menuRepository->getList($searchCriteria);
        $menus = [];

        foreach ($menuList->getItems() as $menu) {
            $identifier = $menu->getIdentifier();

            if (!isset($menus[$identifier])) {
                $menus[$identifier] = $this->convertData($menu);
                $this->loadedMenus[$identifier] = $menu;
            }
        }

        return $menus;
    }

    public function get(string $identifier, int $storeId): ?MenuInterface
    {
        if (isset($this->loadedMenus[$identifier])) {
            return $this->loadedMenus[$identifier];
        }

        $searchCriteria = $this->prepareSearchCriteriaBuilder($identifier, $storeId)
            ->setPageSize(1)
            ->create();

        $menuList = $this->menuRepository->getList($searchCriteria)->getItems();

        return $menuList ? reset($menuList) : null;
    }

    /**
     * @param string|array $identifier
     */
    private function prepareSearchCriteriaBuilder($identifier, int $storeId): SearchCriteriaBuilder
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField(MenuInterface::STORE_ID)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $this->searchCriteriaBuilder
            ->addFilter(MenuInterface::IS_ACTIVE, 1)
            ->addFilter(MenuInterface::STORE_ID, [$storeId, Store::DEFAULT_STORE_ID], 'in')
            ->setSortOrders([$sortOrder]);

        if ($identifier || is_numeric($identifier)) {
            if (!is_array($identifier)) {
                $identifier = [(string) $identifier];
            }

            $this->searchCriteriaBuilder->addFilter(MenuInterface::IDENTIFIER, $identifier, 'in');
        }

        return $this->searchCriteriaBuilder;
    }

    private function convertData(MenuInterface $menu): array
    {
        return [
            MenuInterface::MENU_ID => (int) $menu->getId(),
            MenuInterface::IDENTIFIER => $menu->getIdentifier(),
            MenuInterface::TITLE => $menu->getTitle(),
            MenuInterface::CSS_CLASS => $menu->getCssClass(),
            MenuInterface::CREATION_TIME => $menu->getCreationTime(),
            MenuInterface::UPDATE_TIME => $menu->getUpdateTime()
        ];
    }
}
