<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Catalog
{
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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getCategory($categoryId): ?CategoryInterface
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
        } catch (NoSuchEntityException $exception) {
            $category = null;
        }

        $this->cachedCategories[$categoryId] = $category;

        return $category;
    }

    /**
     * @param string $sku
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getProduct($sku): ?ProductInterface
    {
        if (isset($this->cachedProducts[$sku])) {
            return $this->cachedProducts[$sku];
        }

        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $exception) {
            $product = null;
        }

        $this->cachedProducts[$sku] = $product;

        return $product;
    }
}
