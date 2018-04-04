<?php

namespace Snowdog\Menu\Model\ResourceModel\NodeType;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as CoreProduct;
use Magento\Framework\EntityManager\MetadataPool;

class Product extends AbstractNode
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        ResourceConnection $resource,
        MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($resource);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function fetchConfigData()
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $identifierField = $metadata->getIdentifierField();
        $connection = $this->getConnection('read');

        $select = $connection->select()->from(
            ['e' => $this->getTable('catalog_product_entity')],
            [$identifierField, 'sku']
        );

        return $connection->fetchAll($select);
    }

    /**
     * @param int   $storeId
     * @param array $productIds
     *
     * @return array
     */
    public function fetchData($storeId = Store::DEFAULT_STORE_ID, $productIds = [])
    {
        $connection = $this->getConnection('read');
        $table = $this->getTable('url_rewrite');
        $select = $connection
            ->select()
            ->from($table, ['entity_id', 'request_path'])
            ->where('entity_type = ?', 'product')
            ->where('redirect_type = ?', 0)
            ->where('store_id = ?', $storeId)
            ->where('entity_id IN (' . implode(',', $productIds) . ')');

        return $connection->fetchPairs($select);
    }

    /**
     * @param int $websiteId
     * @param int $customerGroupId
     * @param array $productIds
     * @return array
     */
    public function fetchPriceData($websiteId, $customerGroupId, $productIds = [])
    {
        $connection = $this->getConnection('read');
        $table = $this->getTable('catalog_product_index_price');
        $select = $connection
            ->select()
            ->from($table, ['entity_id', 'final_price'])
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('website_id = ?', $websiteId)
            ->where('entity_id IN (' . implode(',', $productIds) . ')');

        return $connection->fetchPairs($select);
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function fetchImageData($productIds = [])
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $linkField = $metadata->getLinkField();

        $connection = $this->getConnection('read');
        $nameAttributeId = $this->getAttributeIdByCode($connection, 'image');

        $table = $this->getTable('catalog_product_entity_varchar');
        $select = $connection->select()
            ->from($table, [$linkField, 'value'])
            ->where('attribute_id = ?', $nameAttributeId)
            ->where('store_id = ?', 0)
            ->where($linkField .' IN (' . implode(',', $productIds) . ')');

        return $connection->fetchPairs($select);
    }

    /**
     * @param AdapterInterface $connection
     * @param string $code
     * @return array|string
     */
    private function getAttributeIdByCode($connection, $code)
    {
        $select = $connection->select()->from(
            ['a' => $this->getTable('eav_attribute')],
            ['attribute_id']
        )->join(
            ['t' => $this->getTable('eav_entity_type')],
            't.entity_type_id = a.entity_type_id',
            []
        )->where('t.entity_type_code = ?', CoreProduct::ENTITY)->where(
            'a.attribute_code = ?',
            $code
        );

        return $connection->fetchOne($select);
    }
}
