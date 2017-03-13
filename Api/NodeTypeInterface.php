<?php

namespace Snowdog\Menu\Api;


use Magento\Framework\View\Element\BlockInterface;

interface NodeTypeInterface extends BlockInterface
{
    /**
     * @return string
     */
    public function getJsonConfig();

    /**
     * Fetch additional data required for rendering nodes.
     *
     * Should remember all nodes passed as $nodes param internally and store for use during rendering
     *
     * @param \Snowdog\Menu\Api\Data\NodeInterface[] $nodes
     * @return void
     */
    public function fetchData(array $nodes);

    /**
     * Renders node content.
     *
     * @param int $nodeId ID of node to be rendered (based of data stored during fetchData() call)
     * @param int $level in tree depth
     * @return string
     */
    public function getHtml(int $nodeId, int $level);

    /**
     * Returns label od "add node" button in edit form
     *
     * @return string|\Magento\Framework\Phrase
     */
    public function getAddButtonLabel();
}