<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RobotsTxtExtension extends AbstractExtension
{
    public function __construct(private readonly string $publicDirectory)
    {
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_seo_existing_robots_txt', $this->existingRobotsTxt(...)),
        ];
    }

    /**
     * Returns a string if a physical robots.txt file exists else null
     */
    public function existingRobotsTxt(): ?string
    {
        if (file_exists($this->publicDirectory . '/robots.txt')) {
            return file_get_contents($this->publicDirectory . '/robots.txt');
        }

        return null;
    }
}
