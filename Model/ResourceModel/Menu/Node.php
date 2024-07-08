<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Node extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('snowmenu_node', 'node_id');
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $connection->delete('snowmenu_customer', ['node_id = ?' => $object->getNodeId()]);

        $nodeCustomerGroups = $object->getData('customer_groups');
        if ($nodeCustomerGroups && is_string($nodeCustomerGroups)) {
            $nodeCustomerGroups = json_decode($nodeCustomerGroups);
        }
        $insertData = [];
        foreach ($nodeCustomerGroups ?? [] as $customerGroup) {
            $insertData[] = [
                'node_id' => $object->getNodeId(),
                'group_id' => $customerGroup
            ];
        }
        if ($nodeCustomerGroups) {
            $connection->insertMultiple('snowmenu_customer', $insertData);
        }

        return parent::_afterSave($object);
    }

    public function getFields(): array
    {
        return $this->getConnection()->describeTable($this->getMainTable());
    }
}
