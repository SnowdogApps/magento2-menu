<?php
declare(strict_types=1);

namespace Snowdog\Menu\Service\Menu;

use Magento\Framework\App\RequestInterface;
use Snowdog\Menu\Api\Data\MenuInterface;

class Hydrator
{
    /**
     * Maps Request data to Menu object. Introduce `after` plugin to add extra fields
     *
     * @param MenuInterface $menu
     * @param RequestInterface $request
     * @return MenuInterface
     */
    public function mapRequest(MenuInterface $menu, RequestInterface $request): MenuInterface
    {
        $params = $request->getParams();
        $menu->setTitle($params[MenuInterface::TITLE]);
        $menu->setIdentifier($params[MenuInterface::IDENTIFIER]);
        $menu->setCssClass($params[MenuInterface::CSS_CLASS]);
        $menu->setIsActive($params[MenuInterface::IS_ACTIVE]);

        return $menu;
    }
}
