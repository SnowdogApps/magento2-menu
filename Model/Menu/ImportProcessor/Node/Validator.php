<?php

namespace Snowdog\Menu\Model\Menu\ImportProcessor\Node;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Model\Menu\ExportProcessor;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\NodeTypeProvider;

class Validator
{
    const REQUIRED_FIELDS = [
        NodeInterface::TYPE
    ];

    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    public function __construct(
        Catalog $nodeCatalog,
        NodeTypeProvider $nodeTypeProvider
    ) {
        $this->nodeCatalog = $nodeCatalog;
        $this->nodeTypeProvider = $nodeTypeProvider;
    }

    /**
     * @throws ValidatorException
     */
    public function validate(array $data, array $treeTrace = [])
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());

        foreach ($data as $nodeNumber => $node) {
            $this->validateRequiredFields($node, $nodeNumber, $treeTrace);
            $this->validateNodeTypes($node, $nodeTypes, $nodeNumber, $treeTrace);
            $this->validateCatalogNode($node, $nodeNumber, $treeTrace);

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
    private function validateCatalogNode(array $node, $nodeNumber, array $treeTrace)
    {
        $isValid = true;

        switch ($node[NodeInterface::TYPE]) {
            case Catalog::PRODUCT_NODE_TYPE:
                $isValid = $this->nodeCatalog->getProduct($node[NodeInterface::CONTENT]);
                break;
            case Catalog::CATEGORY_NODE_TYPE:
            case Catalog::CHILD_CATEGORY_NODE_TYPE:
                $isValid = $this->nodeCatalog->getCategory($node[NodeInterface::CONTENT]);
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
        return [...$treeTrace, $nodeNumber + 1];
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
