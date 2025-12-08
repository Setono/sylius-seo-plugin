<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\Twig\OpenGraphExtension;
use Twig\Test\IntegrationTestCase;

final class OpenGraphExtensionTest extends IntegrationTestCase
{
    private OpenGraph $openGraph;

    protected function setUp(): void
    {
        parent::setUp();

        $this->openGraph = new OpenGraph();
    }

    public function getExtensions(): array
    {
        return [new OpenGraphExtension($this->openGraph)];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/open_graph';
    }
}
