<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model;

class VueProvider
{
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
            $data[] = sprintf('vue!Snowdog_Menu/vue/menu-type/%s', $component);
        }

        return $data;
    }
}
