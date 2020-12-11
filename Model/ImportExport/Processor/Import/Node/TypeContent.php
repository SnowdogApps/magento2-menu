<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Catalog;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Cms;
use Snowdog\Menu\Model\ImportExport\Processor\NodeTypes;

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
     * @param mixed $content
     * @return mixed
     */
    public function get(string $type, $content)
    {
        switch ($type) {
            case NodeTypes::PRODUCT:
                $product = $this->catalog->getProduct($content);
                $content = (int) $product->getId();

                break;
            case NodeTypes::CMS_BLOCK:
                $block = $this->cms->getBlock($content);
                $content = $block->getIdentifier();

                break;
            case NodeTypes::CMS_PAGE:
                $page = $this->cms->getPage($content);
                $content = $page->getIdentifier();

                break;
        }

        return $content;
    }
}
