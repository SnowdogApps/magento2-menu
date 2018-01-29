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
        $identifierField = $this->eavStructureWrapper->getCategoryIdentifierField();
        $linkField = $this->eavStructureWrapper->getCategoryLinkField();
        $connection = $this->getConnection('read');

        $select = $connection->select()->from(
            ['a' => $this->getTable('eav_attribute')],
            ['attribute_id']
        )->join(
            ['t' => $this->getTable('eav_entity_type')],
            't.entity_type_id = a.entity_type_id',
            []
        )->where('t.entity_type_code = ?', CoreCategory::ENTITY)->where(
            'a.attribute_code = ?',
            'name'
        );

        $nameAttributeId = $connection->fetchOne($select);

        $select = $connection->select()->from(
            ['e' => $this->getTable('catalog_category_entity')],
            [$identifierField, 'parent_id']
        )->join(
            ['v' => $this->getTable('catalog_category_entity_varchar')],
            'v.' . $linkField . ' = e.' . $linkField . ' AND v.store_id = 0 
            AND v.attribute_id = ' . $nameAttributeId,
            ['name' => 'v.value']
        )->where(
            'e.level > 0'
        )->order(
            'e.level ASC'
        )->order(
            'e.position ASC'
        )->order(
            'e.' . $linkField . ' DESC'
        );

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
        $connection = $this->getConnection('read');
        $table = $this->getTable('url_rewrite');
        $select = $connection
            ->select()
            ->from($table, ['entity_id', 'request_path'])
            ->where('entity_type = ?', 'category')
            ->where('redirect_type = ?', 0)
            ->where('store_id = ?', $storeId)
            ->where('entity_id IN (' . implode(',', $categoryIds) . ')');

        return $connection->fetchPairs($select);
    }
}