<?php

namespace Snowdog\Menu\Plugin\Model\ResourceModel\Menu;

use Magento\Framework\Model\AbstractModel;
use Snowdog\Menu\Model\Menu\Cache as MenuCache;

class Save
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
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    public function saveAndInvalidatePageCache(AbstractModel $model, callable $proceed)
    {
        $isPageCacheClearable = $model->hasDataChanges();
        $result = $proceed($model);

        $this->menuCache->invalidatePageCache($isPageCacheClearable);

        return $result;
    }
}
