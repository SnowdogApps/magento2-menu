<?php
declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Node;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductName extends Action implements HttpPostActionInterface
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
    }

    public function execute()
    {
        $storeId = (int) $this->_request->getParam('store_id', 0);
        $productId = $this->_request->getParam('product_id');
        if (empty($productId)) {
            return $this->getMissingProductIdResult();
        }

        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            return $this->getProductNotFoundResult();
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $result->setData(['product_name' => $product->getName()]);
    }

    private function getMissingProductIdResult(): ResultInterface
    {
        return $this->resultFactory
            ->create(ResultFactory::TYPE_JSON)
            ->setHttpResponseCode(400)
            ->setData(['message' => __('Missing required product_id param')]);
    }

    private function getProductNotFoundResult(): ResultInterface
    {
        return $this->resultFactory
            ->create(ResultFactory::TYPE_JSON)
            ->setHttpResponseCode(404)
            ->setData(['message' => __('Product not found')]);
    }
}
