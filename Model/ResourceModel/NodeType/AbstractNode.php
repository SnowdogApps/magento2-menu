<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model\ResourceModel\NodeType;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Store\Model\Store;

abstract class AbstractNode extends AbstractResource
{
    /**
     * @var ResourceConnection
     */
    private $_resources;
    /**
     * @var array
     */
    private $_tables = [];

    /**
     * AbstractNode constructor.
     *
     * @param ResourceConnection          $resource
     * @param \Magento\Framework\Profiler $profiler
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->_resources = $resource;
        parent::__construct();
    }

    /**
     * Fetch additional data required for rendering nodes.
     *
     * @param array $nodes
     *
     * @return mixed
     */
    public abstract function fetchData($storeId = Store::DEFAULT_STORE_ID, $params = []);

    /**
     * @inheritDoc
     */
    public abstract function fetchConfigData();

    /**
     * Get real table name for db table, validated by db adapter
     *
     * @param string $tableName
     *
     * @return string
     * @api
     */
    public function getTable($tableName)
    {
        if (is_array($tableName)) {
            list($tableName, $entitySuffix) = $tableName;
        } else {
            $entitySuffix = null;
        }

        if ($entitySuffix !== null) {
            $tableName .= '_' . $entitySuffix;
        }

        if (!isset($this->_tables[$tableName])) {
            $this->_tables[$tableName] = $this->_resources->getTableName(
                $tableName,
                ResourceConnection::DEFAULT_CONNECTION
            );
        }

        return $this->_tables[$tableName];
    }

    /**
     * Get connection
     *
     * @param string $resourceName
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection($resourceName = ResourceConnection::DEFAULT_CONNECTION)
    {
        return $this->_resources->getConnection($resourceName);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
    }
}