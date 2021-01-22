<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\File;

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
     * @throws ValidatorException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function parse(string $data): array
    {
        try {
            return YamlComponent::parse($data);
        } catch (ParseException $exception) {
            throw new ValidatorException(__('Unable to parse the YAML string: %1', $exception->getMessage()));
        }
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function dump(array $data): string
    {
        return YamlComponent::dump($data, self::INLINE_LEVEL, self::INDENTATION);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function isHashArray(array $data): bool
    {
        return YamlInline::isHash($data);
    }
}
