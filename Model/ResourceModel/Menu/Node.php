<?php
namespace Snowdog\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Node extends AbstractDb
{
    const BULK_INSERT_BATCH_SIZE = 500;

    protected function _construct()
    {
        $this->_init('snowmenu_node', 'node_id');
    }

    /**
     * @param array $nodes
     * @throws Exception
     */
    public function insertMultiple(array $nodes)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $connection->beginTransaction();

        try {
            foreach (array_chunk($nodes, self::BULK_INSERT_BATCH_SIZE) as $chunk) {
                $connection->insertMultiple($table, $chunk);
            }

            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }
}
