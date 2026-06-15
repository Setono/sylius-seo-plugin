<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Renderer;

use Twig\Environment;

final readonly class RobotsTxtRenderer implements RobotsTxtRendererInterface
{
    public function __construct(private Environment $twig)
    {
    }

    public function render(string $robotsTxt): string
    {
        return $this->twig->render($this->twig->createTemplate($robotsTxt));
    }
}
