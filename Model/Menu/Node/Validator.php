<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\Menu\Node;

use Snowdog\Menu\Model\Menu\Node\Validator\CategoryChild as CategoryChildValidator;
use Snowdog\Menu\Model\Menu\Node\Validator\Product as ProductValidator;

class Validator
{
    /**
     * @var ProductValidator
     */
    private $product;

    /**
     * @var CategoryChildValidator
     */
    private $categoryChildValidator;

    public function __construct(
        ProductValidator $product,
        CategoryChildValidator $categoryChildValidator
    ) {
        $this->product = $product;
        $this->categoryChildValidator = $categoryChildValidator;
    }

    public function validate(array $node): void
    {
        switch ($node['type']) {
            case 'product':
                $this->product->validate($node);
                break;
            case 'category_child':
                $this->categoryChildValidator->validate($node);
                break;
        }
    }
}
