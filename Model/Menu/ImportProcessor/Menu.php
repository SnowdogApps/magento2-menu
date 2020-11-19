<?php

namespace Snowdog\Menu\Model\Menu\ImportProcessor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\MenuInterfaceFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;

class Menu
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MenuInterfaceFactory
     */
    private $menuFactory;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var Menu\Store
     */
    private $store;

    /**
     * @var Menu\Validator
     */
    private $validator;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MenuInterfaceFactory $menuFactory,
        MenuRepositoryInterface $menuRepository,
        Menu\Store $store,
        Menu\Validator $validator
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->menuFactory = $menuFactory;
        $this->menuRepository = $menuRepository;
        $this->store = $store;
        $this->validator = $validator;
    }

    /**
     * @return MenuInterface
     */
    public function createMenu(array $data, array $stores)
    {
        $menu = $this->menuFactory->create();

        $menu->setData($this->getProcessedMenuData($data));
        $this->menuRepository->save($menu);
        $menu->saveStores($this->getStoreIds($stores));

        return $menu;
    }

    public function validateImportData(array $data)
    {
        $this->validator->validate($data);
    }

    /**
     * @param string $identifier
     * @return string
     */
    private function getNewMenuIdentifier($identifier)
    {
        $menus = $this->getMenuListByIdentifier($identifier);
        if (!$menus) {
            return $identifier;
        }

        $identifiers = [];
        foreach ($menus as $menu) {
            $identifiers[$menu->getIdentifier()] = $menu->getId();
        }

        $idNumber = 1;
        $newIdentifier = $identifier . '-' . $idNumber;

        while (isset($identifiers[$newIdentifier])) {
            $newIdentifier = $identifier . '-' . (++$idNumber);
        }

        return $newIdentifier;
    }

    /**
     * @param string $identifier
     * @return array
     */
    private function getMenuListByIdentifier($identifier)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(MenuInterface::IDENTIFIER, "${identifier}%", 'like')
            ->create();

        return $this->menuRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @return array
     */
    private function getProcessedMenuData(array $data)
    {
        $data[MenuInterface::IDENTIFIER] = $this->getNewMenuIdentifier($data[MenuInterface::IDENTIFIER]);

        if (isset($data[MenuInterface::IS_ACTIVE])) {
            $data[MenuInterface::IS_ACTIVE] = (bool) $data[MenuInterface::IS_ACTIVE];
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getStoreIds(array $storeCodes)
    {
        $storeIds = [];

        foreach ($storeCodes as $storeCode) {
            $storeIds[] = $this->store->get($storeCode)->getId();
        }

        return $storeIds;
    }
}
