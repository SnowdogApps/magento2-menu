<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    protected function _construct()
    {
        $this->_init(
            \Snowdog\Menu\Model\Menu::class,
            \Snowdog\Menu\Model\ResourceModel\Menu::class
        );
    }

    public function addStoresData()
    {
        foreach ($this->getItems() as $menu) {
            $menu->addData(['stores' => $menu->getStores()]);
        }

        return $this;
    }

    public function addGridStoresData(): self
    {
        foreach ($this->getItems() as $menu) {
            $menu->addData(['store_id' => $menu->getStores()]);
        }

        return $this;
    }
}
