<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export\Node;

use Snowdog\Menu\Model\ImportExport\Processor\Export\Node\Type\Catalog;
use Snowdog\Menu\Model\ImportExport\Processor\NodeTypes;

class TypeContent
{
    /**
     * @var Catalog
     */
    private $catalog;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @param mixed $content
     * @return mixed
     */
    public function get(string $type, $content)
    {
        switch ($type) {
            case NodeTypes::PRODUCT:
                $product = $this->catalog->getProduct((int) $content);
                $content = $product->getSku();

                break;
        }

        return $content;
    }
}
