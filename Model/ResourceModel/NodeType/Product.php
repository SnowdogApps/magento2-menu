<?php

namespace Snowdog\Menu\Model\ResourceModel\NodeType;

use Magento\Store\Model\Store;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ProductMetadataInterface as ProductMetadata;

class Product extends AbstractNode
{
    /**
     * @var CollectionFactory
     */
    private $productCollection;

    /**
     * @var ProductMetadata
     */
    private $productMetadata;

    /**
     * @var array
     */
    private $productFieldsByEdition = [
        'Community' => [
            'entity_id' => 'entity_id'
        ],
        'Enterprise' => [
            'entity_id' => 'row_id'
        ]
    ];

    public function __construct(
        ResourceConnection $resource,
        CollectionFactory $productCollection,
        ProductMetadata $productMetadata
    ) {
        $this->productCollection = $productCollection;
        $this->productMetadata = $productMetadata;
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

    /**
     * @param int $storeId
     * @param array $productIds
     * @return array
     */
    public function fetchTitleData($storeId = Store::DEFAULT_STORE_ID, $productIds = [])
    {
        $entity_id = $this->getField('entity_id');

        $connection = $this->getConnection('read');
        $select = $connection
            ->select()
            ->from(
                ['p' => $this->getTable('catalog_product_entity_varchar')],
                ["{$entity_id}", 'value']
            )
            ->joinLeft(
                ['e' => $this->getTable('eav_attribute')],
                'e.attribute_id = p.attribute_id',
                []
            )
            ->where('p.store_id = ?', $storeId)
            ->where("p.{$entity_id} IN (?)", $productIds)
            ->where('e.attribute_code = ?', 'name');

        return $connection->fetchPairs($select);
    }

    /**
     * @param string $name
     * @return string
     */
    private function getField($name)
    {
        $edition = $this->getMagentoEdition();

        return $this->productFieldsByEdition[$edition][$name];
    }

    private function getMagentoEdition()
    {
        return $this->productMetadata->getEdition();
    }
}
