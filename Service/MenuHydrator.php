<?php
declare(strict_types=1);

namespace Snowdog\Menu\Service;

use Magento\Framework\App\RequestInterface;
use Snowdog\Menu\Api\Data\MenuInterface;

class MenuHydrator
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
        $menu->setTitle($request->getParam('title'));
        $menu->setIdentifier($request->getParam('identifier'));
        $menu->setCssClass($request->getParam('css_class'));

        return $menu;
    }
}
