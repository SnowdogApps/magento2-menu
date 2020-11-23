<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Catalog
{
    const CATEGORY_NODE_TYPE = 'category';
    const CHILD_CATEGORY_NODE_TYPE = 'category_child';
    const PRODUCT_NODE_TYPE = 'product';

    const ROOT_CATEGORY_ID = 1;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var array
     */
    private $cachedCategories = [];

    /**
     * @var array
     */
    private $cachedProducts = [];

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    public function getCategory($categoryId)
    {
        if (!ctype_digit((string) $categoryId) || $categoryId <= self::ROOT_CATEGORY_ID) {
            return null;
        }

        $categoryId = (int) $categoryId;
        if (isset($this->cachedCategories[$categoryId])) {
            return $this->cachedCategories[$categoryId];
        }

        try {
            $category = $this->categoryRepository->get($categoryId);
            $this->cachedCategories[$categoryId] = $category;
        } catch (NoSuchEntityException $exception) {
            $category = null;
        }

        return $category;
    }

    /**
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getProduct($sku)
    {
        if (isset($this->cachedProducts[$sku])) {
            return $this->cachedProducts[$sku];
        }

        try {
            $product = $this->productRepository->get($sku);
            $this->cachedProducts[$sku] = $product;
        } catch (NoSuchEntityException $exception) {
            $product = null;
        }

        return $product;
    }
}
