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
     * 3rd party extensions have to provide full path
     *
     * @var array
     */
    private $externalComponents;

    /**
     * @param array $components
     * @param array $externalComponents
     */
    public function __construct(
        array $components = [],
        array $externalComponents = []
    ) {
        $this->components = $components;
        $this->externalComponents = $externalComponents;
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
        foreach ($this->externalComponents as $externalComponent) {
            $data[] = $externalComponent;
        }

        return $data;
    }
}
