<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Catalog;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Cms;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator\TreeTrace;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;
use Snowdog\Menu\Model\ImportExport\Processor\NodeTypes;
use Snowdog\Menu\Model\NodeTypeProvider;

class NodeType
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var Cms
     */
    private $cms;

    /**
     * @var TreeTrace
     */
    private $treeTrace;

    /**
     * @var ValidationAggregateError
     */
    private $validationAggregateError;

    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var array
     */
    private $nodeTypes = [];

    public function __construct(
        Catalog $catalog,
        Cms $cms,
        TreeTrace $treeTrace,
        ValidationAggregateError $validationAggregateError,
        NodeTypeProvider $nodeTypeProvider
    ) {
        $this->catalog = $catalog;
        $this->cms = $cms;
        $this->treeTrace = $treeTrace;
        $this->validationAggregateError = $validationAggregateError;
        $this->nodeTypeProvider = $nodeTypeProvider;
    }

    /**
     * @param int|string $nodeId
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function validate(array $node, $nodeId, array $treeTrace): void
    {
        if (!isset($node[NodeInterface::TYPE]) || $node[NodeInterface::TYPE] === '') {
            return;
        }

        try {
            $this->validateType($node[NodeInterface::TYPE], $nodeId, $treeTrace);
        } catch (ValidationAggregateError $exception) {
            return;
        }

        $this->validateNodeTypeContent($node, $nodeId, $treeTrace);
    }

    /**
     * @param int|string $nodeId
     * @throws ValidationAggregateError
     */
    private function validateType(string $type, $nodeId, array $treeTrace): void
    {
        if (!in_array($type, $this->getNodeTypes())) {
            $treeTraceBreadcrumbs = $this->treeTrace->getBreadcrumbs($treeTrace, $nodeId);

            $this->validationAggregateError->addError(
                __('Node "%1" type "%2" is invalid.', $treeTraceBreadcrumbs, $type)
            );

            throw $this->validationAggregateError; // Terminate the node type validation task.
        }
    }

    /**
     * @param int|string $nodeId
     */
    private function validateNodeTypeContent(array $node, $nodeId, array $treeTrace): void
    {
        if (!isset($node[NodeInterface::CONTENT]) || $node[NodeInterface::CONTENT] === '') {
            return;
        }

        $isValid = true;

        switch ($node[NodeInterface::TYPE]) {
            case NodeTypes::PRODUCT:
                $isValid = $this->catalog->getProduct($node[NodeInterface::CONTENT]);
                break;
            case NodeTypes::CATEGORY:
            case NodeTypes::CHILD_CATEGORY:
                $isValid = $this->catalog->getCategory($node[NodeInterface::CONTENT]);
                break;
            case NodeTypes::CMS_BLOCK:
                $isValid = $this->cms->getBlock($node[NodeInterface::CONTENT]);
                break;
            case NodeTypes::CMS_PAGE:
                $isValid = $this->cms->getPage($node[NodeInterface::CONTENT]);
                break;
        }

        if (!$isValid) {
            $this->validationAggregateError->addError(
                __(
                    'Node "%1" %2 identifier "%3" is invalid.',
                    $this->treeTrace->getBreadcrumbs($treeTrace, $nodeId),
                    $node[NodeInterface::TYPE],
                    $node[NodeInterface::CONTENT]
                )
            );
        }
    }

    private function getNodeTypes(): array
    {
        if (!$this->nodeTypes) {
            $this->nodeTypes = array_keys($this->nodeTypeProvider->getLabels());
        }

        return $this->nodeTypes;
    }
}
