<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Catalog;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type\Cms;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;
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
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var array
     */
    private $nodeTypes = [];

    public function __construct(Catalog $catalog, Cms $cms, NodeTypeProvider $nodeTypeProvider)
    {
        $this->catalog = $catalog;
        $this->cms = $cms;
        $this->nodeTypeProvider = $nodeTypeProvider;
    }

    public function validate(array $node)
    {
        $this->validateType($node[NodeInterface::TYPE]);
        $this->validateNodeTypeContent($node);
    }

    /**
     * @param string $type
     * @throws ValidatorException
     */
    private function validateType($type)
    {
        if (!in_array($type, $this->getNodeTypes())) {
            throw new ValidatorException(
                __('Node "%%1" type is invalid.', Validator::TREE_TRACE_BREADCRUMBS_ERROR_PLACEHOLDER)
            );
        }
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
                    Validator::TREE_TRACE_BREADCRUMBS_ERROR_PLACEHOLDER,
                    $node[NodeInterface::TYPE],
                    $node[NodeInterface::CONTENT]
                )
            );
        }
    }

    /**
     * @return array
     */
    private function getNodeTypes()
    {
        if (!$this->nodeTypes) {
            $this->nodeTypes = array_keys($this->nodeTypeProvider->getLabels());
        }

        return $this->nodeTypes;
    }
}
