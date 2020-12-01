<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\Menu\Node;

class Validator
{
    /**
     * @var Validator\Product
     */
    private $product;

    public function __construct(Validator\Product $product)
    {
        $this->product = $product;
    }

    public function validate(array $node): bool
    {
        $isValid = true;

        switch ($node['type']) {
            case 'product':
                $isValid = $this->product->validate($node);
                break;
        }

        return $isValid;
    }
}
