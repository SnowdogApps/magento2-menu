<?php
declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Node;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\InputException;

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

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute()
    {
        $storeId = (int) $this->_request->getParam('store_id', 0);
        $productId = $this->_request->getParam('product_id');
        if (empty($productId)) {
            throw new InputException(__("Missing required product_id param"));
        }
        $product = $this->productRepository->getById($productId, false, $storeId);
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $result->setData(['product_name' => $product->getName()]);
    }
}
