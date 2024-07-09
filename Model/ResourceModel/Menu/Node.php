<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\SerializerInterface;

class Node extends AbstractDb
{
    protected $serializer;

    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        $connectionName = null
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('snowmenu_node', 'node_id');
    }

    protected function _afterSave(AbstractModel $object)
    {
        $connection = $this->getConnection();
        $connection->delete('snowmenu_customer', ['node_id = ?' => $object->getNodeId()]);

        $nodeCustomerGroups = $object->getData('customer_groups');
        if ($nodeCustomerGroups && is_string($nodeCustomerGroups)) {
            $nodeCustomerGroups = $this->serializer->unserialize($nodeCustomerGroups);
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
