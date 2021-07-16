<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\Menu\Grid;

use Snowdog\Menu\Model\ResourceModel\Menu\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * @inheritDoc
     */
    public function addStoresData()
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
        $this->addStoresData();
        return parent::_afterLoad();
    }
}
