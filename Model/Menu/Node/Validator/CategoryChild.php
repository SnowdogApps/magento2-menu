<?php

namespace Snowdog\Menu\Model\Menu\Node\Validator;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Catalog;

class CategoryChild
{
    /**
     * @var Catalog
     */
    private $catalog;

    public function __construct(
        Catalog $catalog
    ) {
        $this->catalog = $catalog;
    }

    /**
     * @throws ValidatorException
     */
    public function validate(array $node)
    {
        $this->validateParentCategoryField($node);
    }

    /**
     * @throws ValidatorException
     */
    private function validateParentCategoryField(array $node)
    {
        if (!isset($node['content']) || $node['content'] == '') {
            throw new ValidatorException(__('%1 parent category is required.', $this->getNodeTitle($node)));
        }

        $childCategoryId = $node['content'];
        $isValid = $this->catalog->getCategory($childCategoryId);
        if (!$isValid) {
            throw new ValidatorException(__('%1 parent category is invalid.', $this->getNodeTitle($node)));
        }
    }

    private function getNodeTitle($node): string
    {
        return isset($node['title']) && $node['title'] !== ''
            ? 'Node "' . $node['title'] . '"' : 'A node';
    }
}
