<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\AggregateError;

class Validator
{
    const TREE_TRACE_BREADCRUMBS_ERROR_PLACEHOLDER = 'tree_trace_breadcrumbs';

    const REQUIRED_FIELDS = [
        NodeInterface::TYPE
    ];

    /**
     * @var Validator\NodeType
     */
    private $nodeTypeValidator;

    /**
     * @var AggregateError
     */
    private $aggregateError;

    public function __construct(Validator\NodeType $nodeTypeValidator, AggregateError $aggregateError)
    {
        $this->nodeTypeValidator = $nodeTypeValidator;
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
                $this->aggregateError->addError(
                    $this->getTreeTracedExceptionMessage($exception, $this->getTreeTrace($treeTrace, $nodeNumber))
                );
            }

            if (isset($node[ExtendedFields::NODES])) {
                $this->validate($node[ExtendedFields::NODES], $this->getTreeTrace($treeTrace, $nodeNumber));
            }
        }
    }

    /**
     * @param int $nodeNumber
     */
    private function runValidationTasks(array $node, $nodeNumber, array $treeTrace)
    {
        $this->validateRequiredFields($node, $nodeNumber, $treeTrace);
        $this->nodeTypeValidator->validate($node);
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
                    $this->getTreeTraceBreadcrumbs($this->getTreeTrace($treeTrace, $nodeNumber)),
                    implode('", "', $missingFields)
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
     * @return string
     */
    private function getTreeTraceBreadcrumbs(array $treeTrace)
    {
        return implode(' > ', $treeTrace);
    }

    /**
     * @return string
     */
    private function getTreeTracedExceptionMessage(\Exception $exception, array $treeTrace)
    {
        return __(
            $exception->getMessage(),
            [self::TREE_TRACE_BREADCRUMBS_ERROR_PLACEHOLDER => $this->getTreeTraceBreadcrumbs($treeTrace)]
        );
    }
}
