<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

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
                throw new ValidatorException(
                    $this->getTreeTracedExceptionMessage($exception, $this->getTreeTrace($treeTrace, $nodeNumber))
                );
            }

            if (isset($node[ExtendedFields::NODES])) {
                $this->validate($node[ExtendedFields::NODES], $this->getTreeTrace($treeTrace, $nodeNumber));
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
            [self::ERROR_TREE_TRACE_BREADCRUMBS => $this->getTreeTraceBreadcrumbs($treeTrace)]
        );
    }
}
