<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\AggregateError;

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
     * @var AggregateError
     */
    private $aggregateError;

    public function __construct(
        Validator\NodeType $nodeTypeValidator,
        Validator\TreeTrace $treeTrace,
        AggregateError $aggregateError
    ) {
        $this->nodeTypeValidator = $nodeTypeValidator;
        $this->treeTrace = $treeTrace;
        $this->aggregateError = $aggregateError;
    }

    /**
     * @throws ValidatorException
     */
    public function validate(array $data, array $treeTrace = [])
    {
        foreach ($data as $nodeNumber => $node) {
            try {
                $this->runValidationTasks($node, $nodeNumber, $treeTrace);
            } catch (ValidatorException $exception) {
                $this->aggregateError->addError($exception->getMessage());
            }

            if (isset($node[ExtendedFields::NODES])) {
                $this->validate($node[ExtendedFields::NODES], $this->treeTrace->get($treeTrace, $nodeNumber));
            }
        }
    }

    /**
     * @param int $nodeNumber
     */
    private function runValidationTasks(array $node, $nodeNumber, array $treeTrace)
    {
        $this->validateRequiredFields($node, $nodeNumber, $treeTrace);
        $this->nodeTypeValidator->validate($node, $nodeNumber, $treeTrace);
    }

    /**
     * @param int $nodeNumber
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
            $this->aggregateError->addError(
                __(
                    'The following node "%1" required import fields are missing: "%2".',
                    $this->treeTrace->getBreadcrumbs($treeTrace, $nodeNumber),
                    implode('", "', $missingFields)
                )
            );
        }
    }
}
