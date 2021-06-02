<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\Menu;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;
use Snowdog\Menu\Model\GraphQl\Resolver\Identity as EntityIdentity;
use Snowdog\Menu\Model\Menu;

class Identity implements IdentityInterface
{
    /**
     * @var EntityIdentity
     */
    private $identity;

    public function __construct(EntityIdentity $identity)
    {
        $this->identity = $identity;
    }

    /**
     * @inheritDoc
     */
    public function getIdentities(array $resolvedData): array
    {
        return $this->identity->getIdentities($resolvedData, Menu::IDENTIFIER, Menu::CACHE_TAG);
    }
}
