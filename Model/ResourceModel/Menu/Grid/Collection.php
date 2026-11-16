<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\Menu\Grid;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ResourceModel\Menu\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * @inheritDoc
     */
    public function addStoresData()
    {
        $items = $this->getItems();
        if (!$items) {
            return $this;
        }

        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getTable(MenuInterface::STORE_RELATION_TABLE),
                [MenuInterface::MENU_ID, MenuInterface::STORE_ID]
            )
            ->where(MenuInterface::MENU_ID . ' IN (?)', $this->getColumnValues(MenuInterface::MENU_ID))
            ->order([MenuInterface::MENU_ID, MenuInterface::STORE_ID]);
        $stores = [];
        foreach ($connection->fetchAll($select) as $row) {
            $stores[$row[MenuInterface::MENU_ID]][] = $row[MenuInterface::STORE_ID];
        }

        foreach ($items as $menu) {
            $menu->addData([MenuInterface::STORE_ID => $stores[$menu->getId()] ?? []]);
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
