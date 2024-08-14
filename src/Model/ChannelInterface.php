<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

interface ChannelInterface
{
    public function getRobotsTxt(): ?string;

    public function setRobotsTxt(?string $robotsTxt): void;
}
