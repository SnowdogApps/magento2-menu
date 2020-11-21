<?php

namespace Snowdog\Menu\Model\ImportExport\ImportProcessor\Node;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;
use Snowdog\Menu\Model\NodeTypeProvider;

class Validator
{
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

    public function validate(array $data, array $treeTrace = [])
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());

        foreach ($data as $nodeNumber => $node) {
            $this->validateRequiredFields($node, $nodeNumber, $treeTrace);
            $this->validateNodeTypes($node, $nodeTypes, $nodeNumber, $treeTrace);
            $this->validateNodeTypeContent($node, $nodeNumber, $treeTrace);

            if (isset($node[ExportProcessor::NODES_FIELD])) {
                $this->validate($node[ExportProcessor::NODES_FIELD], $this->getTreeTrace($treeTrace, $nodeNumber));
            }
        }
    }

    /**
     * @param int $nodeNumber
     * @throws ValidatorException
     */
    private function validateRequiredFields(array $node, $nodeNumber, array $treeTrace)
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
                    'The following node "%1" required import fields are missing: "%2".',
                    $this->getTreeTraceLabel($treeTrace, $nodeNumber),
                    implode('", "', $missingFields)
                )
            );
        }
    }

    /**
     * @param int $nodeNumber
     * @throws ValidatorException
     */
    private function validateNodeTypes(array $node, array $nodeTypes, $nodeNumber, array $treeTrace)
    {
        if (!in_array($node[NodeInterface::TYPE], $nodeTypes)) {
            throw new ValidatorException(
                __('Node "%1" type is invalid.', $this->getTreeTraceLabel($treeTrace, $nodeNumber))
            );
        }
    }

    /**
     * @param int $nodeNumber
     * @throws ValidatorException
     */
    private function validateNodeTypeContent(array $node, $nodeNumber, array $treeTrace)
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
                    'Node "%1" %2 identifier "%3" is invalid.',
                    $this->getTreeTraceLabel($treeTrace, $nodeNumber),
                    $node[NodeInterface::TYPE],
                    $node[NodeInterface::CONTENT]
                )
            );
        }
    }

    /**
     * @param int $nodeNumber
     * @return array
     */
    private function getTreeTrace(array $treeTrace, $nodeNumber)
    {
        $treeTrace[] = $nodeNumber + 1;
        return $treeTrace;
    }

    /**
     * @param int $nodeNumber
     * @return string
     */
    private function getTreeTraceLabel(array $treeTrace, $nodeNumber)
    {
        return implode(' > ', $this->getTreeTrace($treeTrace, $nodeNumber));
    }
}
