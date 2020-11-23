<?php

namespace Snowdog\Menu\Model\ImportExport\ImportProcessor\FieldProcessor;

class Boolean
{
    const TRUE_VALUES = [1, 'true'];

    /**
     * @param mixed $data
     * @return bool
     */
    public function getValue($data)
    {
        $data = strtolower((string) $data);
        return in_array($data, self::TRUE_VALUES);
    }
}
