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
        $params = $request->getParams();
        $menu->setTitle($params['title']);
        $menu->setIdentifier($params['identifier']);
        $menu->setCssClass($params['css_class']);
        $menu->setIsActive($params['is_active']);

        return $menu;
    }
}
