<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel\NodeTranslation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Snowdog\Menu\Model\NodeTranslation;
use Snowdog\Menu\Model\ResourceModel\NodeTranslation as NodeTranslationResource;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(NodeTranslation::class, NodeTranslationResource::class);
    }
}
