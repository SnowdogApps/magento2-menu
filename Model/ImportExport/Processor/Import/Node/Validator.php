<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Export;

class Validator
{
    const ERROR_TREE_TRACE_BREADCRUMBS = 'tree_trace_breadcrumbs';

    const REQUIRED_FIELDS = [
        NodeInterface::TYPE
    ];

    /**
     * @var Validator\NodeType
     */
    private $nodeTypeValidator;

    public function __construct(Validator\NodeType $nodeTypeValidator)
    {
        $this->nodeTypeValidator = $nodeTypeValidator;
    }

    /**
     * @throws ValidatorException
     */
    public function validate(array $data, array $treeTrace = [])
    {
        foreach ($data as $nodeNumber => $node) {
            try {
                $this->runValidationTasks($node);
            } catch (ValidatorException $exception) {
                $treeTrace[] = $nodeNumber + 1;

                throw new ValidatorException(
                    __($exception->getMessage(), [self::ERROR_TREE_TRACE_BREADCRUMBS => implode(' > ', $treeTrace)])
                );
            }

            if (isset($node[Export::NODES_FIELD])) {
                $treeTrace[] = $nodeNumber + 1;
                $this->validate($node[Export::NODES_FIELD], $treeTrace);
            }
        }
    }

    private function runValidationTasks(array $node)
    {
        $this->validateRequiredFields($node);
        $this->nodeTypeValidator->validate($node);
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
}
