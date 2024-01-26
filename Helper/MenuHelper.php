<?php

namespace Snowdog\Menu\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\EntityManager\MetadataPool;
use Snowdog\Menu\Api\Data\MenuInterface;

class MenuHelper extends AbstractHelper
{
    private MetadataPool $metadataPool;

    public function __construct(
        MetadataPool $metadataPool,
        Context $context,
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($context);
    }

    public function getLinkField(): string
    {
        $metadata = $this->metadataPool->getMetadata(MenuInterface::class);
        return $metadata->getLinkField();
    }

    public function getLinkValue(MenuInterface $menu): string
    {
        return (string) $menu->getData($this->getLinkField());
    }
}
