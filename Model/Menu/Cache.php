<?php

namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\App\Cache\TypeListInterface as CacheTypeList;
use Magento\PageCache\Model\Cache\Type as PageCacheType;

class Cache
{
    /**
     * @var CacheTypeList
     */
    private $cacheTypeList;

    /**
     * @var bool
     */
    private $isPageCacheInvalidated = false;

    public function __construct(CacheTypeList $cacheTypeList)
    {
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * @param bool $isClearable
     */
    public function invalidatePageCache($isClearable = true)
    {
        if ($isClearable && $this->isPageCacheInvalidated) {
            $this->cacheTypeList->invalidate(PageCacheType::TYPE_IDENTIFIER);
            $this->isPageCacheInvalidated = true;
        }
    }
}
