<?php

namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\App\Cache\TypeListInterface as CacheTypeList;
use Magento\PageCache\Model\Cache\Type as PageCacheType;
use Magento\PageCache\Model\Config as PageCacheConfig;

class Cache
{
    /**
     * @var CacheTypeList
     */
    private $cacheTypeList;

    /**
     * @var PageCacheConfig
     */
    private $config;

    /**
     * @var bool
     */
    private $isPageCacheInvalidated = false;

    public function __construct(CacheTypeList $cacheTypeList, PageCacheConfig $config)
    {
        $this->cacheTypeList = $cacheTypeList;
        $this->config = $config;
    }

    /**
     * @param bool $isClearable
     */
    public function invalidatePageCache($isClearable = true)
    {
        if ($isClearable && !$this->isPageCacheInvalidated && $this->config->isEnabled()) {
            $this->cacheTypeList->invalidate(PageCacheType::TYPE_IDENTIFIER);
            $this->isPageCacheInvalidated = true;
        }
    }
}
