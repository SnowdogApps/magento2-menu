<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

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

    public function __construct(Menu $menu, Node $node)
    {
        $this->menu = $menu;
        $this->node = $node;
    }

    public function getExportData(int $menuId): array
    {
        $data = $this->menu->getData($menuId);
        $nodes = $this->node->getList($menuId);

        if ($nodes) {
            $data[ExtendedFields::NODES] = $nodes;
        }

        return $data;
    }
}
