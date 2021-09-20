<?php

namespace Snowdog\Menu\Model\NodeType;

use Magento\Customer\Model\Session;
use Magento\Framework\Profiler;
use Magento\Store\Model\StoreManagerInterface;
use Snowdog\Menu\Model\Menu\Node\Image\File as NodeImage;
use Snowdog\Menu\Model\TemplateResolver;

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
     * @var TemplateResolver
     */
    private $templateResolver;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Snowdog\Menu\Model\ResourceModel\NodeType\Product::class);
        parent::_construct();
    }

    public function __construct(
        Profiler $profiler,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        NodeImage $nodeImage,
        TemplateResolver $templateResolver
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->templateResolver = $templateResolver;
        parent::__construct($profiler, $nodeImage);
    }

    /**
     * @inheritDoc
     */
    public function fetchData(array $nodes, $storeId)
    {
        $this->profiler->start(__METHOD__);

        $localNodes = [];
        $productIds = [];

        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomer()->getGroupId();

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $productIds[] = (int)$node->getContent();
        }

        $resource = $this->getResource();
        $productImages = $resource->fetchImageData($storeId, $productIds);
        $productUrls = $resource->fetchData($storeId, $productIds);
        $productPrices = $resource->fetchPriceData($websiteId, $customerGroupId, $productIds);
        $productTitles = $resource->fetchTitleData($storeId, $productIds);
        $this->profiler->stop(__METHOD__);

        return [
            $localNodes,
            $productUrls,
            $productPrices,
            $productImages,
            $productTitles
        ];
    }

    /**
     * @inheritDoc
     */
    public function fetchConfigData()
    {
        $this->profiler->start(__METHOD__);

        $data = [
            'snowMenuNodeCustomTemplates' => [
                'defaultTemplate' => 'product',
                'options' => $this->templateResolver->getCustomTemplateOptions('product'),
                'message' => __('Template not found'),
            ],
            'snowMenuSubmenuCustomTemplates' => [
                'defaultTemplate' => 'sub_menu',
                'options' => $this->templateResolver->getCustomTemplateOptions('sub_menu'),
                'message' => __('Template not found'),
            ],
        ];

        $this->profiler->stop(__METHOD__);

        return $data;
    }
}
