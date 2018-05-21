<?php

namespace Snowdog\Menu\Model\ResourceModel\NodeType;

use Magento\Store\Model\Store;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Product extends AbstractNode
{
    /**
     * @var CollectionFactory
     */
    private $productCollection;

    public function __construct(
        ResourceConnection $resource,
        CollectionFactory $productCollection
    ) {
        $this->productCollection = $productCollection;
        parent::__construct($resource);
    }

    /**
     * @param int   $storeId
     * @param array $productIds
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
            ->where('entity_id IN (?)', $productIds)
            ->where('metadata IS NULL');

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
            ->where('entity_id IN (?)', $productIds);

        return $connection->fetchPairs($select);
    }

    /**
     * @param int $storeId
     * @param array $productIds
     * @return array
     */
    public function fetchImageData($storeId, $productIds = [])
    {
        $collection = $this->productCollection->create();
        $collection->addAttributeToSelect(['thumbnail'])
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addStoreFilter($storeId);

        $imageData = [];
        foreach ($collection->getData() as $data) {
            $imageData[$data['entity_id']] = $data['thumbnail'] ?? '';
        }

        return $imageData;
    }

    /**
     * @inheritDoc
     */
    public function fetchConfigData()
    {
        return [];
    }
}
