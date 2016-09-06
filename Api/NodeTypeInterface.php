<?php

namespace Snowdog\Menu\Api;

interface NodeTypeInterface
{
    public function fetchData(array $nodes);

    public function getHtml(int $nodeId, int $level);

    public function getAddButtonLabel();

    public function toHtml();
}