<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Snowdog\Menu\Model\TemplateResolver;
use Magento\Framework\View\Element\Template\Context;
use Snowdog\Menu\Model\NodeType\Product as ModelProduct;

class Product extends AbstractNode
{
    /**
     * @var string
     */
    protected $defaultTemplate = 'menu/node_type/product.phtml';

    /**
     * @var string
     */
    protected $nodeType = 'product';

    /**
     * @var array
     */
    protected $nodes;

    /**
     * @var array
     */
    protected $productUrls;

    /**
     * @var array
     */
    protected $productPrices;

    /**
     * @var array
     */
    protected $productImages;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ModelProduct
     */
    private $productModel;

    /**
     * @var String
     */
    private $mediaUrl;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ModelProduct $productModel,
        TemplateResolver $templateResolver,
        array $data = []
    ) {
        parent::__construct($context, $templateResolver, $data);
        $this->coreRegistry = $coreRegistry;
        $this->productModel = $productModel;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getCurrentProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $data = $this->productModel->fetchConfigData();

        return $data;
    }

    /**
     * @param array $nodes
     */
    public function fetchData(array $nodes)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        list(
            $this->nodes,
            $this->productUrls,
            $this->productPrices,
            $this->productImages
        ) = $this->productModel->fetchData($nodes, $storeId);
    }

    /**
     * @param int $nodeId
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isCurrentProduct($nodeId)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $productId = (int)$node->getContent();
        $currentProduct = $this->getCurrentProduct();

        return $currentProduct
            ? $currentProduct->getId() == $productId
            : false;
    }

    /**
     * @param int $nodeId
     * @param int|null $storeId
     * @return string|false
     * @throws \InvalidArgumentException
     */
    public function getProductUrl($nodeId, $storeId = null)
    {
        $productUrlPath = $this->getProductData($this->productUrls, $nodeId);

        if ($productUrlPath) {
            $baseUrl = $this->_storeManager->getStore($storeId)->getBaseUrl();

            return $baseUrl . $productUrlPath;
        }

        return false;
    }

    /**
     * @param int $nodeId
     * @return double|false
     * @throws \InvalidArgumentException
     */
    public function getProductPrice($nodeId)
    {
        return $this->getProductData($this->productPrices, $nodeId);
    }

    /**
     * @param int $nodeId
     * @return null|string
     */
    public function getProductImage($nodeId)
    {
        $image = $this->getProductData($this->productImages, $nodeId);

        if (!$image) {
            return null;
        }

        return $this->getMediaUrl('catalog/product' . $image);
    }

    /**
     * @param array $data
     * @param int $nodeId
     * @return false|string|double
     */
    public function getProductData($data, $nodeId)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $productId = (int)$node->getContent();

        if (isset($data[$productId])) {
            return $data[$productId];
        }

        return false;
    }

    /**
     * @param int $nodeId
     * @param int $level
     * @param int $storeId
     *
     * @return string
     */
    public function getHtml($nodeId, $level, $storeId = null)
    {
        $classes = $level == 0 ? 'level-top' : '';
        $node = $this->nodes[$nodeId];
        $url = $this->getProductUrl($nodeId, $storeId);
        $title = $node->getTitle();

        return <<<HTML
<a href="$url" class="$classes" role="menuitem"><span>$title</span></a>
HTML;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getMediaUrl($path)
    {
        if (!$this->mediaUrl) {
            $this->mediaUrl = $this->_storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        }

        return $this->mediaUrl . $path;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __("Product");
    }
}
