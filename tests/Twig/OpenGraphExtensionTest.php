<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\Twig\OpenGraphExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Test\IntegrationTestCase;

final class OpenGraphExtensionTest extends IntegrationTestCase
{
    private OpenGraph $openGraph;

    protected function setUp(): void
    {
        parent::setUp();

        $this->openGraph = new OpenGraph();
    }

    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/Fixtures/open_graph';
    }

    /**
     * @return list<ExtensionInterface>
     */
    protected function getExtensions(): array
    {
        return [new OpenGraphExtension($this->openGraph)];
    }
}
