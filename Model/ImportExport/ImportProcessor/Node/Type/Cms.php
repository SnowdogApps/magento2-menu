<?php

namespace Snowdog\Menu\Model\ImportExport\ImportProcessor\Node\Type;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Cms
{
    const BLOCK_NODE_TYPE = 'cms_block';
    const PAGE_NODE_TYPE = 'cms_page';

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var array
     */
    private $cachedBlocks = [];

    /**
     * @var array
     */
    private $cachedPages = [];

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        PageRepositoryInterface $pageRepository
    ) {
        $this->blockRepository = $blockRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param string $identifier
     * @return \Magento\Cms\Api\Data\BlockInterface|null
     */
    public function getBlock($identifier)
    {
        if (isset($this->cachedBlocks[$identifier])) {
            return $this->cachedBlocks[$identifier];
        }

        try {
            $block = $this->blockRepository->getById($identifier);
            $this->cachedBlocks[$identifier] = $block;
        } catch (NoSuchEntityException $exception) {
            $block = null;
        }

        return $block;
    }

    /**
     * @param string $identifier
     * @return \Magento\Cms\Api\Data\PageInterface|null
     */
    public function getPage($identifier)
    {
        if (isset($this->cachedPages[$identifier])) {
            return $this->cachedPages[$identifier];
        }

        try {
            $page = $this->pageRepository->getById($identifier);
            $this->cachedPages[$identifier] = $page;
        } catch (NoSuchEntityException $exception) {
            $page = null;
        }

        return $page;
    }
}
