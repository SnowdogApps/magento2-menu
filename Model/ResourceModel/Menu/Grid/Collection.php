<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\Menu\Grid;

use Snowdog\Menu\Model\ResourceModel\Menu\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function addGridStoresData(): self
    {
        foreach ($this->getItems() as $menu) {
            $menu->addData(['store_id' => $menu->getStores()]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        $this->addGridStoresData();
        return parent::_afterLoad();
    }
}
