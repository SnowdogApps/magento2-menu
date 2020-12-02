<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import;

use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class SampleData
{
    const DOWNLOAD_FILE_ID = 'sample';

    /**
     * @var SampleData\Menu
     */
    private $menu;

    /**
     * @var SampleData\Node
     */
    private $node;

    public function __construct(SampleData\Menu $menu, SampleData\Node $node)
    {
        $this->menu = $menu;
        $this->node = $node;
    }

    public function getSampleData(): array
    {
        $data = $this->menu->getData();

        $data[ExtendedFields::STORES] = SampleData\Menu::STORES_DATA;
        $data[ExtendedFields::NODES] = $this->node->getData();

        return $data;
    }
}
