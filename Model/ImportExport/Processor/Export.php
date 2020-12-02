<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

class Export
{
    /**
     * @var Export\Menu
     */
    private $menu;

    /**
     * @var Export\Node
     */
    private $node;

    public function __construct(Export\Menu $menu, Export\Node $node)
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
