<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor\Field;

class Value
{
    const BOOLEAN_TYPES = ['smallint', 'tinyint'];
    const BOOLEAN_FIELD_DEFAULT_VALUE = 'Valid values: <1 | 0>';

    public function getValue(string $field, array $fieldDescription, array $defaultData = []): string
    {
        switch (true) {
            case array_key_exists($field, $defaultData):
                $data = (string) $defaultData[$field];
                break;
            case in_array($fieldDescription['DATA_TYPE'], self::BOOLEAN_TYPES):
                $data = self::BOOLEAN_FIELD_DEFAULT_VALUE;
                break;
            default:
                $data = '[Type: ' . $fieldDescription['DATA_TYPE'] . ']';
        }

        return $data;
    }
}
