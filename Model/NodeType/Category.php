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

use Magento\Framework\Profiler;
use Snowdog\Menu\Helper\EavStructureWrapper;

class Category extends AbstractNode
{
    /**
     * @var EavStructureWrapper
     */
    protected $eavStructureWrapper;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\ResourceModel\NodeType\Category');
        parent::_construct();
    }

    /**
     * Category constructor.
     *
     * @param Profiler $profiler
     * @param EavStructureWrapper $eavStructureWrapper
     */
    public function __construct(
        Profiler $profiler,
        EavStructureWrapper $eavStructureWrapper
    ) {
        $this->eavStructureWrapper = $eavStructureWrapper;
        parent::__construct($profiler);
    }

    /**
     * @inheritDoc
     */
    public function fetchConfigData()
    {
        $this->profiler->start(__METHOD__);
        $identifierField = $this->eavStructureWrapper->getCategoryIdentifierField();

        $data = $this->getResource()->fetchConfigData();
        $labels = [];

        foreach ($data as $row) {
            if (isset($labels[$row['parent_id']])) {
                $label = $labels[$row['parent_id']];
            } else {
                $label = [];
            }
            $label[] = $row['name'];
            $labels[$row[$identifierField]] = $label;
        }

        $options = [];

        foreach ($labels as $id => $label) {
            $label = implode(' > ', $label);
            $options[$label] = $id;
        }

        $data = [
            'snowMenuAutoCompleteField' => [
                'type'    => 'category',
                'options' => $options,
                'message' => __('Category not found'),
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
        $categoryIds = [];

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $categoryIds[] = (int)$node->getContent();
        }

        $categoryUrls = $this->getResource()->fetchData($storeId, $categoryIds);

        $this->profiler->stop(__METHOD__);

        return [$localNodes, $categoryUrls];
    }
}