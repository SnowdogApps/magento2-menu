<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model\NodeType;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Profiler;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Category extends AbstractNode
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var CollectionFactory
     */
    private $categoryCollection;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\ResourceModel\NodeType\Category');
        parent::_construct();
    }

    /**
     * Category constructor.
     *
     * @param Profiler $profiler
     * @param MetadataPool $metadataPool
     * @param CollectionFactory $categoryCollection
     */
    public function __construct(
        Profiler $profiler,
        MetadataPool $metadataPool,
        CollectionFactory $categoryCollection
    ) {
        $this->metadataPool = $metadataPool;
        $this->categoryCollection = $categoryCollection;
        parent::__construct($profiler);
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function fetchConfigData()
    {
        $this->profiler->start(__METHOD__);
        $metadata = $this->metadataPool->getMetadata(CategoryInterface::class);
        $identifierField = $metadata->getIdentifierField();

        $data = $this->getResource()->fetchConfigData();
        $labels = [];

        foreach ($data as $row) {
            if (isset($labels[$row['parent_id']])) {
                $label = $labels[$row['parent_id']];
            } else {
                $label = [];
            }
            $label[] = $row['name'];
            $labels[$row[$identifierField]] = $label;
        }
        
        $fieldOptions = [];
        foreach ($labels as $id => $label) {
            $fieldOptions[] = [
                'label' => $label = implode(' > ', $label),
                'value' => $id
            ];
        }

        $data = [
            'snowMenuAutoCompleteField' => [
                'type'    => 'category',
                'options' => $fieldOptions,
                'message' => __('Category not found'),
            ],
        ];

        $this->profiler->stop(__METHOD__);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function fetchData(array $nodes, $storeId)
    {
        $this->profiler->start(__METHOD__);

        $localNodes = [];
        $categoryIds = [];

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $categoryIds[] = (int)$node->getContent();
        }

        $categoryUrls = $this->getResource()->fetchData($storeId, $categoryIds);
        $categories = $this->getCategories($storeId, $categoryIds);

        $this->profiler->stop(__METHOD__);

        return [$localNodes, $categoryUrls, $categories];
    }

    /**
     * @param int|string|\Magento\Store\Model\Store $store
     * @param array $categoryIds
     * @return array
     */
    public function getCategories($store, array $categoryIds)
    {
        $return = [];
        $categories = $this->categoryCollection->create()
            ->addAttributeToSelect('*')
            ->setStoreId($store)
            ->addFieldToFilter(
                'entity_id',
                ['in' => $categoryIds]
            );

        foreach ($categories as $category) {
            $return[$category->getId()] = $category;
        }

        return $return;
    }
}
