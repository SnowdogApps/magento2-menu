<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model;

class VueProvider
{
    /**
     * @var array
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * @return array
     */
    public function getComponents(): array
    {
        $data = [];
        foreach ($this->providers as $vueComponent) {
            $data[] = sprintf('vue!Snowdog_Menu/vue/menu-type/%s', $vueComponent);
        }

        return $data;
    }
}
