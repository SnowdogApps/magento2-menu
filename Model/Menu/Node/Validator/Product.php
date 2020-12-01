<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\Menu\Node\Validator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class Product
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        MessageManagerInterface $messageManager
    ) {
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
    }

    public function validate(array $node): bool
    {
        try {
            $this->validateProductId($node);
            $result = true;
        } catch (ValidatorException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $result = false;
        }

        return $result;
    }

    /**
     * @throws ValidatorException
     */
    private function validateProductId(array $node): void
    {
        if (!isset($node['content']) || $node['content'] === '') {
            throw new ValidatorException(__('Node catalog product ID is required.'));
        }

        if (!$this->getProductById($node['content'])) {
            throw new ValidatorException(__('Node catalog product ID "%1" is invalid.', $node['content']));
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
}
