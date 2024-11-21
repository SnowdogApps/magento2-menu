<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\ResourceConnection;

class Node extends AbstractDb
{
    protected $serializer;
    protected $resource;

    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        ResourceConnection $resource,
        $connectionName = null
    ) {
        $this->serializer = $serializer;
        $this->resource = $resource;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('snowmenu_node', 'node_id');
    }

    protected function _afterSave(AbstractModel $object)
    {
        $connection = $this->resource->getConnection();
        $tableName  = $this->resource->getTableName('snowmenu_customer');
        $connection->delete($tableName, ['node_id = ?' => $object->getNodeId()]);

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
            $connection->insertMultiple($tableName, $insertData);
        }

        return parent::_afterSave($object);
    }

    public function getFields(): array
    {
        return $this->getConnection()->describeTable($this->getMainTable());
    }
}
