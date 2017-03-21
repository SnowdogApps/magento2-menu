<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model\NodeType;

class CmsPage extends AbstractNode
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\ResourceModel\NodeType\CmsPage');
        parent::_construct();
    }

    /**
     * @inheritDoc
     */
    public function fetchConfigData()
    {
        $this->profiler->start(__METHOD__);

        $options = $this->getResource()->fetchConfigData();

        $data = [
            'snowMenuAutoCompleteField' => [
                'type'    => 'cms_page',
                'options' => $options,
                'message' => __('CMS Page not found'),
            ],
        ];

        $this->profiler->stop(__METHOD__);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function fetchData(array $nodes, $storeId)
    {
        $this->profiler->start(__METHOD__);

        $localNodes = [];
        $pagesCodes = [];

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $pagesCodes[] = $node->getContent();
        }

        /** @var \Snowdog\Menu\Model\ResourceModel\NodeType\CmsPage $resource */
        $resource = $this->getResource();
        $pageIds = $resource->getPageIds($storeId, $pagesCodes);
        $pageUrls = $resource->fetchData($storeId, $pageIds);


        $this->profiler->stop(__METHOD__);

        return [$localNodes, $pageIds, $pageUrls];
    }
}