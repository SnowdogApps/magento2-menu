<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Node;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\Template\FilterProvider;

class TypeModel
{
    const TYPES = ["category", "product", "cms_page", "cms_block"];

    const TYPE_CMS_BLOCK = 'cms_block';

    /**
     * @var \Snowdog\Menu\Model\ResourceModel\NodeType\AbstractNode[]
     */
    private $typeModels = [];

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    public function __construct(
        FilterProvider $filterProvider,
        array $typeModels = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->typeModels = $typeModels;
    }

    public function getModels($type, $modelIds, $storeId)
    {
        if (isset($this->typeModels[$type])) {
            $models = $this->typeModels[$type]->fetchData($storeId, $modelIds);

            if ($type === self::TYPE_CMS_BLOCK) {
                return $this->prepareCmsBlockModels($models, (int) $storeId);
            }

            return $models;
        }

        return [];
    }

    /**
     * @param array $models
     * @param int $storeId
     * @return array
     */
    private function prepareCmsBlockModels(array $models, int $storeId): array
    {
        $preparedModels = [];

        foreach ($models as $model) {
            if (!isset($model['identifier'])) {
                continue;
            }

            $preparedModels[$model['identifier']] = $this->filterProvider
                ->getBlockFilter()
                ->setStoreId($storeId)
                ->filter($model['content'] ?? '');
        }

        return $preparedModels;
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
