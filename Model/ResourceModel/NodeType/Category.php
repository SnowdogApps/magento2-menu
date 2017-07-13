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

use Magento\Catalog\Model\Category as CoreCategory;
use Magento\Framework\App\ResourceConnection;
use Snowdog\Menu\Helper\EavStructureWrapper;
use Magento\Store\Model\Store;


class Category extends AbstractNode
{
    /**
     * @var EavStructureWrapper
     */
    protected $eavStructureWrapper;

    /**
     * Category constructor.
     *
     * @param ResourceConnection $resource
     * @param EavStructureWrapper $eavStructureWrapper
     */
    public function __construct(
        ResourceConnection $resource,
        EavStructureWrapper $eavStructureWrapper

    ) {
        $this->eavStructureWrapper = $eavStructureWrapper;
        parent::__construct($resource);
    }

    /**
     * @return array
     */
    public function fetchConfigData()
    {
        $eavColumnName = $this->eavStructureWrapper->getEntityColumnName();
        $connection = $this->getConnection('read');

        $select = $connection->select()->from(
            ['a' => $connection->getTableName('eav_attribute')],
            ['attribute_id']
        )->join(
            ['t' => $connection->getTableName('eav_entity_type')],
            't.entity_type_id = a.entity_type_id',
            []
        )->where('t.entity_type_code = ?', CoreCategory::ENTITY)->where(
            'a.attribute_code = ?',
            'name'
        );

        $nameAttributeId = $connection->fetchOne($select);

        $select = $connection->select()->from(
            ['e' => $connection->getTableName('catalog_category_entity')],
            [$eavColumnName => 'e.' . $eavColumnName, 'parent_id' => 'e.parent_id']
        )->join(
            ['v' => $connection->getTableName('catalog_category_entity_varchar')],
            'v.' . $eavColumnName . ' = e.' . $eavColumnName . ' AND v.store_id = 0 
            AND v.attribute_id = ' . $nameAttributeId,
            ['name' => 'v.value']
        )->where('e.level > 0')->order('e.level ASC')->order('e.position ASC');

        return $connection->fetchAll($select);
    }

    /**
     * @param int   $storeId
     * @param array $categoryIds
     *
     * @return array
     */
    public function fetchData($storeId = Store::DEFAULT_STORE_ID, $categoryIds = [])
    {
        $eavColumnName = $this->eavStructureWrapper->getEntityColumnName();
        $connection = $this->getConnection('read');
        $table = $connection->getTableName('url_rewrite');
        $select = $connection
            ->select()
            ->from($table, [$eavColumnName, 'request_path'])
            ->where('entity_type = ?', 'category')
            ->where('redirect_type = ?', 0)
            ->where('store_id = ?', $storeId)
            ->where($eavColumnName . ' IN (' . implode(',', $categoryIds) . ')');

        return $connection->fetchPairs($select);
    }
}