<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Node;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Cms\Api\Data\PageInterface;

class TypeModel
{
    const TYPES = ["category", "product", "cms_page"];

    /**
     * @var \Snowdog\Menu\Model\ResourceModel\NodeType\AbstractNode[]
     */
    private $typeModels = [];

    public function __construct(
        array $typeModels = []
    ) {
        $this->typeModels = $typeModels;
    }

    public function getModels($type, $modelIds, $storeId)
    {
        if (isset($this->typeModels[$type])) {
            return $this->typeModels[$type]->fetchData($storeId, $modelIds);
        }

        return [];
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
