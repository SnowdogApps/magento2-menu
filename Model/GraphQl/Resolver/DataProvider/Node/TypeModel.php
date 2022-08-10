<?php

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Node;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class TypeModel
{
    const TYPES = ["category", "product", "cms_page"];
    
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        PageRepositoryInterface $pageRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param $type
     * @param $modelId
     * @param $storeId
     * @return ProductInterface|CategoryInterface|PageInterface|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getModel($type, $modelId, $storeId)
    {
        $model = null;
        switch ($type) {
            case "product":
                $model = $this->productRepository->getById($modelId, false, $storeId);
                break;
            case "category":
                $model = $this->categoryRepository->get($modelId, $storeId);
                break;
            case "cms_page":
                $model = $this->pageRepository->getById($modelId);
                break;
            default:
                break;
        }
        return $model;
    }

    public function getModelUrlKey($type, $model): ?string
    {
        switch ($type) {
            case "product":
                /** @var ProductInterface $model */
                $urlKey = $model->getUrlKey();
                break;
            case "category":
                /** @var CategoryInterface $model */
                $urlKey = $model->getUrlKey();
                break;
            case "cms_page":
                /** @var PageInterface $model */
                $urlKey = $model->getIdentifier();
                break;
            default:
                $urlKey = "";
                break;
        }
        return $urlKey;
    }
}
