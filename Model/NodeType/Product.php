<?php

namespace Snowdog\Menu\Model\NodeType;

use Magento\Customer\Model\Session;
use Magento\Framework\Profiler;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;

class Product extends AbstractNode
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\ResourceModel\NodeType\Product');
        parent::_construct();
    }

    public function __construct(
        Profiler $profiler,
        MetadataPool $metadataPool,
        StoreManagerInterface $storeManager,
        Session $customerSession
    ) {
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        $this->customerSession = $customerSession;
        parent::__construct($profiler);
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function fetchConfigData()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchData(array $nodes, $storeId)
    {
        $this->profiler->start(__METHOD__);

        $localNodes = [];
        $productIds = [];

        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomer()->getGroupId();

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $productIds[] = (int)$node->getContent();
        }
        
        $resource = $this->getResource();
        $productImages = $resource->fetchImageData($storeId, $productIds);
        $productUrls = $resource->fetchData($storeId, $productIds);
        $productPrices = $resource->fetchPriceData($websiteId, $customerGroupId, $productIds);
        $this->profiler->stop(__METHOD__);

        return [$localNodes, $productUrls, $productPrices, $productImages];
    }
}
