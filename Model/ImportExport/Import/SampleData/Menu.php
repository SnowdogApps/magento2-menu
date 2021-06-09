<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ResourceModel\Menu as MenuResource;

class Menu
{
    const EXCLUDED_FIELDS = [
        MenuInterface::MENU_ID,
        MenuInterface::CREATION_TIME,
        MenuInterface::UPDATE_TIME
    ];

    const STORES_DATA = ['<A store code/ID>', '<Another store code/ID>'];

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var MenuResource
     */
    private $menuResource;

    public function __construct(Processor $processor, MenuResource $menuResource)
    {
        $this->processor = $processor;
        $this->menuResource = $menuResource;
    }

    public function getData(): array
    {
        $data = $this->processor->getFieldsData($this->menuResource->getFields(), self::EXCLUDED_FIELDS);
        $data[ExtendedFields::STORES] = self::STORES_DATA;

        return $data;
    }
}
