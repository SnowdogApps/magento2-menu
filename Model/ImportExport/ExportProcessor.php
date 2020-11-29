<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport;

class ExportProcessor
{
    /**
     * @var Processor\Export\Menu
     */
    private $menu;

    /**
     * @var Processor\Export\Node
     */
    private $node;

    public function __construct(Processor\Export\Menu $menu, Processor\Export\Node $node)
    {
        $this->menu = $menu;
        $this->node = $node;
    }

    public function getExportData(int $menuId): array
    {
        $data = $this->menu->getData($menuId);
        $nodes = $this->node->getList($menuId);

        if ($nodes) {
            $data[Processor\ExtendedFields::NODES] = $nodes;
        }

        return $data;
    }
}
