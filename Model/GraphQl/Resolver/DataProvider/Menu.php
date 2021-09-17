<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Store as StoreDataProvider;

class Menu
{
    /**
     * GraphQL type fields.
     */
    const STORES_FIELD = 'stores';

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var StoreDataProvider
     */
    private $storeDataProvider;

    public function __construct(
        MenuRepositoryInterface $menuRepository,
        StoreDataProvider $storeDataProvider
    ) {
        $this->menuRepository = $menuRepository;
        $this->storeDataProvider = $storeDataProvider;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getMenuByIdentifier(string $identifier, int $store): array
    {
        $menu = $this->menuRepository->get($identifier, $store);

        if (!$menu->getId()) {
            throw new NoSuchEntityException(__('Could not find a menu with identifier "%1".', $identifier));
        }

        return $this->convertData($menu);
    }

    private function convertData(MenuInterface $menu): array
    {
        return [
            MenuInterface::MENU_ID => (int) $menu->getId(),
            MenuInterface::IDENTIFIER => $menu->getIdentifier(),
            MenuInterface::TITLE => $menu->getTitle(),
            MenuInterface::CSS_CLASS => $menu->getCssClass(),
            self::STORES_FIELD => $this->storeDataProvider->getCodes($menu->getStores()),
            MenuInterface::CREATION_TIME => $menu->getCreationTime(),
            MenuInterface::UPDATE_TIME => $menu->getUpdateTime()
        ];
    }
}
