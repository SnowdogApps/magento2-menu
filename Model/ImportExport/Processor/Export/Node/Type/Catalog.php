<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export\Node\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Catalog
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var array
     */
    private $cachedProducts = [];

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProduct(int $productId): ProductInterface
    {
        if (isset($this->cachedProducts[$productId])) {
            return $this->cachedProducts[$productId];
        }

        $product = $this->productRepository->getById($productId);
        $this->cachedProducts[$productId] = $product;

        return $product;
    }
}
