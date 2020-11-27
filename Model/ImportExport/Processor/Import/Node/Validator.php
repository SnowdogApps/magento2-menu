<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;
use Snowdog\Menu\Model\ImportExport\Yaml;

class Validator
{
    const REQUIRED_FIELDS = [
        NodeInterface::TYPE
    ];

    /**
     * @var Validator\NodeType
     */
    private $nodeTypeValidator;

    /**
     * @var Validator\TreeTrace
     */
    private $treeTrace;

    /**
     * @var ValidationAggregateError
     */
    private $validationAggregateError;

    public function __construct(
        Validator\NodeType $nodeTypeValidator,
        Validator\TreeTrace $treeTrace,
        ValidationAggregateError $validationAggregateError,
        Yaml $yaml
    ) {
        $this->nodeTypeValidator = $nodeTypeValidator;
        $this->treeTrace = $treeTrace;
        $this->validationAggregateError = $validationAggregateError;
        $this->yaml = $yaml;
    }

    public function validate(array $data, array $treeTrace = []): void
    {
        if ($this->yaml->isHashArray($data)) {
            $this->treeTrace->disableNodeIdAddend();
        }

        foreach ($data as $nodeId => $node) {
            $this->runValidationTasks($node, $nodeId, $treeTrace);

            if (isset($node[ExtendedFields::NODES])) {
                $this->validate($node[ExtendedFields::NODES], $this->treeTrace->get($treeTrace, $nodeId));
            }
        }
    }

    /**
     * @param int|string $nodeId
     */
    private function runValidationTasks(array $node, $nodeId, array $treeTrace): void
    {
        $this->validateRequiredFields($node, $nodeId, $treeTrace);
        $this->nodeTypeValidator->validate($node, $nodeId, $treeTrace);
    }

    /**
     * @param int|string $nodeId
     */
    private function validateRequiredFields(array $node, $nodeId, array $treeTrace): void
    {
        $missingFields = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($node[$field]) || $node[$field] === '') {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            $this->validationAggregateError->addError(
                __(
                    'The following node "%1" required import fields are missing: "%2".',
                    $this->treeTrace->getBreadcrumbs($treeTrace, $nodeId),
                    implode('", "', $missingFields)
                )
            );
        }
    }
}
