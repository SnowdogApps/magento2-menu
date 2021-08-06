<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\Menu\Node\Validator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;

class Product
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function validate(array $node): void
    {
        $this->validateProductId($node);
    }

    /**
     * @throws ValidatorException
     */
    private function validateProductId(array $node): void
    {
        if (!isset($node['content']) || $node['content'] === '') {
            $nodeTitle = $this->getErrorNodeTitle($node);
            throw new ValidatorException(__('%1 catalog product ID is required.', $nodeTitle));
        }

        if (!$this->getProductById($node['content'])) {
            $nodeTitle = $this->getErrorNodeTitle($node);
            throw new ValidatorException(
                __('%1 catalog product ID "%2" is invalid.', $nodeTitle, $node['content'])
            );
        }
    }

    /**
     * @param int $productId
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function getProductById($productId): ?ProductInterface
    {
        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $exception) {
            $product = null;
        }

        return $product;
    }

    private function getErrorNodeTitle(array $node): string
    {
        return isset($node['title']) && $node['title'] !== ''
            ? 'Node "' . $node['title'] . '"' : 'A node';
    }
}
