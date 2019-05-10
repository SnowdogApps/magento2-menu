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

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CoreCategory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\Store;

class Category extends AbstractNode
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param ResourceConnection $resource
     * @param MetadataPool $metadataPool
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        ResourceConnection $resource,
        MetadataPool $metadataPool,
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->metadataPool = $metadataPool;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($resource);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function fetchConfigData()
    {
        $metadata = $this->metadataPool->getMetadata(CategoryInterface::class);
        $identifierField = $metadata->getIdentifierField();
        $linkField = $metadata->getLinkField();
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
        /* @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categories */
        $categories = $this->categoryCollectionFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addIdFilter($categoryIds);

        $data = [];

        /* @var \Magento\Catalog\Model\Category $category */
        foreach ($categories as $category) {
            $data[$category->getId()] = $category;
        }

        return $data;
    }
}
