<?php

namespace Snowdog\Menu\Model\ImportExport;

use Magento\Framework\Exception\ValidatorException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Inline as YamlInline;
use Symfony\Component\Yaml\Yaml as YamlComponent;

class Yaml
{
    const INLINE_LEVEL = 10;
    const INDENTATION = 2;

    const FILE_EXTENSIONS = ['yaml', 'yml'];

    /**
     * @param string $data
     * @throws ValidatorException
     * @return array
     */
    public function parse($data)
    {
        try {
            return YamlComponent::parse($data);
        } catch (ParseException $exception) {
            throw new ValidatorException(__('Unable to parse the YAML string: %1', $exception->getMessage()));
        }
    }

    /**
     * @return string
     */
    public function dump(array $data)
    {
        return YamlComponent::dump($data, self::INLINE_LEVEL, self::INDENTATION);
    }

    /**
     * @return bool
     */
    public function isHashArray(array $data)
    {
        return YamlInline::isHash($data);
    }
}
