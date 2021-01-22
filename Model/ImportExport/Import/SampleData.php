<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import;

use Snowdog\Menu\Model\ImportExport\Import\SampleData\Menu;
use Snowdog\Menu\Model\ImportExport\Import\SampleData\Node;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class SampleData
{
    const DOWNLOAD_FILE_ID = 'sample';

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

    public function get(): array
    {
        $data = $this->menu->getData();
        $data[ExtendedFields::NODES] = $this->node->getData();

        return $data;
    }
}
