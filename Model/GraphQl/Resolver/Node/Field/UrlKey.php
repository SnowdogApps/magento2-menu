<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\Node\Field;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Snowdog\Menu\Api\Data\NodeInterface;

class UrlKey implements ResolverInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return string|null
     * @throws GraphQlNoSuchEntityException
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): ?string {
        $categoryId = $this->getContentCategoryId($value);
        if ($categoryId === null) {
            return null;
        }
        try {
            $storeId = (int) $context->getExtensionAttributes()->getStore()->getId();
            $category = $this->categoryRepository->get($categoryId, $storeId);
            return $this->getUrlKeyWithoutSlash($category->getUrl());
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * If a non-numeric string is stored in the field, the int cast will return 0
     *
     * @param array $value
     * @return int|null
     */
    private function getContentCategoryId(array $value): ?int
    {
        $categoryId = (int) $value[NodeInterface::CONTENT];
        if ($categoryId != 0 ) {
            return $categoryId;
        }
        return null;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getUrlKeyWithoutSlash(string $url): string
    {
        $completePath = parse_url($url, PHP_URL_PATH);
        //The first slash is always returned by the parse_url function, so we have to remove it
        return substr($completePath, 1);
    }
}
