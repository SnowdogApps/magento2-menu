<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;

class Menu
{
    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    public function __construct(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
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
            MenuInterface::CREATION_TIME => $menu->getCreationTime(),
            MenuInterface::UPDATE_TIME => $menu->getUpdateTime()
        ];
    }
}
