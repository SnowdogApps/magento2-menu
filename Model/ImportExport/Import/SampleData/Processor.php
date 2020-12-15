<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData;

class Processor
{
    const BOOLEAN_TYPES = ['smallint', 'tinyint'];
    const BOOLEAN_FIELD_DEFAULT_VALUE = 'Valid values: <1 | 0>';

    const OPTIONAL_FIELD_LABEL = '[optional]';

    public function getFieldsData(array $fields, array $excludedFields = [], array $defaultData = []): array
    {
        $fieldsData = [];
        $excludedFields = array_flip($excludedFields);

        foreach ($fields as $field => $description) {
            if (isset($excludedFields[$field])) {
                continue;
            }

            $optionalFieldLabel = $description['NULLABLE'] ? ' ' . self::OPTIONAL_FIELD_LABEL : '';

            if (array_key_exists($field, $defaultData)) {
                $fieldsData[$field] = $defaultData[$field] . $optionalFieldLabel;
                continue;
            }

            if (in_array($description['DATA_TYPE'], self::BOOLEAN_TYPES)) {
                $fieldsData[$field] = self::BOOLEAN_FIELD_DEFAULT_VALUE;
                continue;
            }

            $fieldsData[$field] = '<' . $description['DATA_TYPE'] . '>' . $optionalFieldLabel;
        }

        return $fieldsData;
    }
}
