<?php

namespace Snowdog\Menu\Plugin\Model\ResourceModel\Menu\Node\AroundSave;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ResourceModel\Menu\Node as NodeResourceModel;
use Snowdog\Menu\Plugin\Model\ResourceModel\Menu\Save as MenuSave;

class InvalidatePageCache
{
    /**
     * @var MenuSave
     */
    private $menuSave;

    public function __construct(MenuSave $menuSave)
    {
        $this->menuSave = $menuSave;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    public function aroundSave(NodeResourceModel $subject, callable $proceed, NodeInterface $node)
    {
        return $this->menuSave->saveAndInvalidatePageCache($node, $proceed);
    }
}
