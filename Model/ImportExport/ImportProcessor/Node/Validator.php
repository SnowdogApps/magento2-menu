<?php

namespace Snowdog\Menu\Model\ImportExport\ImportProcessor\Node;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;
use Snowdog\Menu\Model\NodeTypeProvider;

class Validator
{
    const ERROR_TREE_TRACE_BREADCRUMBS = 'tree_trace_breadcrumbs';

    const REQUIRED_FIELDS = [
        NodeInterface::TYPE
    ];

    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var Cms
     */
    private $cms;

    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    public function __construct(Catalog $catalog, Cms $cms, NodeTypeProvider $nodeTypeProvider)
    {
        $this->catalog = $catalog;
        $this->cms = $cms;
        $this->nodeTypeProvider = $nodeTypeProvider;
    }

    /**
     * @throws ValidatorException
     */
    public function validate(array $data, array $treeTrace = [])
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());

        foreach ($data as $nodeNumber => $node) {
            try {
                $this->runValidationTasks($node, $nodeTypes);
            } catch (ValidatorException $exception) {
                $treeTrace[] = $nodeNumber + 1;

                throw new ValidatorException(
                    __($exception->getMessage(), [self::ERROR_TREE_TRACE_BREADCRUMBS => implode(' > ', $treeTrace)])
                );
            }

            if (isset($node[ExportProcessor::NODES_FIELD])) {
                $treeTrace[] = $nodeNumber + 1;
                $this->validate($node[ExportProcessor::NODES_FIELD], $treeTrace);
            }
        }
    }

    private function runValidationTasks(array $node, array $nodeTypes)
    {
        $this->validateRequiredFields($node);
        $this->validateNodeType($node, $nodeTypes);
    }

    /**
     * @throws ValidatorException
     */
    private function validateRequiredFields(array $node)
    {
        $missingFields = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($node[$field])) {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            throw new ValidatorException(
                __(
                    'The following node "%%1" required import fields are missing: "%2".',
                    self::ERROR_TREE_TRACE_BREADCRUMBS,
                    implode('", "', $missingFields)
                )
            );
        }
    }

    /**
     * @throws ValidatorException
     */
    private function validateNodeType(array $node, array $nodeTypes)
    {
        if (!in_array($node[NodeInterface::TYPE], $nodeTypes)) {
            throw new ValidatorException(
                __('Node "%%1" type is invalid.', self::ERROR_TREE_TRACE_BREADCRUMBS)
            );
        }

        $this->validateNodeTypeContent($node);
    }

    /**
     * @throws ValidatorException
     */
    private function validateNodeTypeContent(array $node)
    {
        $isValid = true;

        switch ($node[NodeInterface::TYPE]) {
            case Catalog::PRODUCT_NODE_TYPE:
                $isValid = $this->catalog->getProduct($node[NodeInterface::CONTENT]);
                break;
            case Catalog::CATEGORY_NODE_TYPE:
            case Catalog::CHILD_CATEGORY_NODE_TYPE:
                $isValid = $this->catalog->getCategory($node[NodeInterface::CONTENT]);
                break;
            case Cms::BLOCK_NODE_TYPE:
                $isValid = $this->cms->getBlock($node[NodeInterface::CONTENT]);
                break;
            case Cms::PAGE_NODE_TYPE:
                $isValid = $this->cms->getPage($node[NodeInterface::CONTENT]);
                break;
        }

        if (!$isValid) {
            throw new ValidatorException(
                __(
                    'Node "%%1" %2 identifier "%3" is invalid.',
                    self::ERROR_TREE_TRACE_BREADCRUMBS,
                    $node[NodeInterface::TYPE],
                    $node[NodeInterface::CONTENT]
                )
            );
        }
    }
}
