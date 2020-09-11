<?php

namespace Snowdog\Menu\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /** @var array */
    private $loadedData = [];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        /** @var Menu $menu */
        foreach ($items as $menu) {
            $menu->addData(['stores' => $menu->getStores()]);
            $this->loadedData[$menu->getMenuId()] = $menu->getData();
        }

        return $this->loadedData;
    }
}
