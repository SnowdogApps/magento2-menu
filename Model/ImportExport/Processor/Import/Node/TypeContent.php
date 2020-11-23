<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Catalog;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Cms;

class TypeContent
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var Cms
     */
    private $cms;

    public function __construct(Catalog $catalog, Cms $cms)
    {
        $this->catalog = $catalog;
        $this->cms = $cms;
    }

    /**
     * @param string $type
     * @param mixed $content
     * @return mixed
     */
    public function get($type, $content)
    {
        switch ($type) {
            case Catalog::PRODUCT_NODE_TYPE:
                $product = $this->catalog->getProduct($content);
                $content = $product->getId();

                break;
            case Cms::BLOCK_NODE_TYPE:
                $block = $this->cms->getBlock($content);
                $content = $block->getIdentifier();

                break;
            case Cms::PAGE_NODE_TYPE:
                $page = $this->cms->getPage($content);
                $content = $page->getIdentifier();

                break;
        }

        return $content;
    }
}
