<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model;

class VueProvider
{
    const COMPONENT_PATH = 'vue!Snowdog_Menu/vue/menu-type/%s';

    /**
     * @var array
     */
    private $components;

    /**
     * @param array $components
     */
    public function __construct(
        array $components = []
    ) {
        $this->components = $components;
    }

    /**
     * @return array
     */
    public function getComponents(): array
    {
        $data = [];
        foreach ($this->components as $component) {
            $data[] = sprintf(self::COMPONENT_PATH, $component);
        }

        return $data;
    }
}
