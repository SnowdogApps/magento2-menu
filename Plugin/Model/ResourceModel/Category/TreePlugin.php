<?php

declare(strict_types=1);

namespace Snowdog\Menu\Plugin\Model\ResourceModel\Category;

use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Framework\App\RequestInterface;

class TreePlugin
{
    private RequestInterface $request;

    public function __construct(
        RequestInterface $request,
    ) {
        $this->request = $request;
    }

    public function beforeAddCollectionData(Tree $subject, $collection = null): array
    {
        $postData = $this->request->getPost();
        $storeId = $postData['store_id'];

        $collection->setProductStoreId(
            $storeId
        )->setStoreId(
            $storeId
        );

        return [$collection];
    }
}
