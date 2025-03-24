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
        $filename = $this->publicDirectory . '/robots.txt';

        if (file_exists($filename) && is_readable($filename)) {
            return file_get_contents($filename);
        }

        return null;
    }
}
