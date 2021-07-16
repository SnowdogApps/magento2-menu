<?php

declare(strict_types=1);

namespace Snowdog\Menu\Ui\DataProvider\Menu\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Snowdog\Menu\Model\Menu;

class MenuDataProvider extends AbstractDataProvider
{
    /** @var array */
    private $loadedData = [];

    /**
     * @var PoolInterface
     */
    private $pool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create()->addStoresData();
        $this->pool = $pool;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        /** @var Menu $menu */
        foreach ($items as $menu) {
            $this->loadedData[$menu->getId()] = $menu->getData();
        }

        return $this->loadedData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
