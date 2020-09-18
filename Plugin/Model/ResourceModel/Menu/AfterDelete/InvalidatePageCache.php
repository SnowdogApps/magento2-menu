<?php

namespace Snowdog\Menu\Plugin\Model\ResourceModel\Menu\AfterDelete;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Snowdog\Menu\Model\Menu\Cache as MenuCache;
use Snowdog\Menu\Model\ResourceModel\Menu as MenuResourceModel;

class InvalidatePageCache
{
    /**
     * @var MenuCache
     */
    private $menuCache;

    public function __construct(MenuCache $menuCache)
    {
        $this->menuCache = $menuCache;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return AbstractDb
     */
    public function afterDelete(MenuResourceModel $subject, AbstractDb $result)
    {
        $this->menuCache->invalidatePageCache();
        return $result;
    }
}
