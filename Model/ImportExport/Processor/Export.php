<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Snowdog\Menu\Helper\MenuHelper;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Menu;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class Export
{
    /**
     * @var Menu
     */
    private $menu;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var MenuHelper
     */
    private $menuHelper;

    public function __construct(Menu $menu, Node $node, MenuHelper $menuHelper)
    {
        $this->menu = $menu;
        $this->node = $node;
        $this->menuHelper = $menuHelper;
    }

    public function getExportData(int $menuId): array
    {
        $data = $this->menu->getData($menuId);
        $nodes = $this->node->getList((int) $data[$this->menuHelper->getLinkField()]);

        if ($nodes) {
            $data[ExtendedFields::NODES] = $nodes;
        }

        return $data;
    }
}
