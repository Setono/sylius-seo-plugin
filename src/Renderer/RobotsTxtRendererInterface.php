<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Renderer;

interface RobotsTxtRendererInterface
{
    public function render(string $robotsTxt): string;
}
