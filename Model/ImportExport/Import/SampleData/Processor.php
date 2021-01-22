<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData;

use Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor\Field as FieldProcessor;

class Processor
{
    /**
     * @var FieldProcessor
     */
    private $fieldProcessor;

    public function __construct(FieldProcessor $fieldProcessor)
    {
        $this->fieldProcessor = $fieldProcessor;
    }

    public function getFieldsData(array $fields, array $excludedFields = [], array $defaultData = []): array
    {
        $fieldsData = [];
        $excludedFields = array_flip($excludedFields);

        foreach ($fields as $field => $fieldDescription) {
            if (!isset($excludedFields[$field])) {
                $fieldsData[$field] = $this->fieldProcessor->getData($field, $fieldDescription, $defaultData);
            }
        }

        return $fieldsData;
    }
}
