<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\Twig\OpenGraphExtension;

final class OpenGraphExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_open_graph_as_global(): void
    {
        $openGraph = new OpenGraph();
        $extension = new OpenGraphExtension($openGraph);

        $globals = $extension->getGlobals();

        self::assertArrayHasKey('openGraph', $globals);
        self::assertSame($openGraph, $globals['openGraph']);
    }

    /**
     * @test
     */
    public function it_registers_render_function(): void
    {
        $extension = new OpenGraphExtension(new OpenGraph());

        $functions = $extension->getFunctions();

        self::assertCount(1, $functions);
        self::assertSame('setono_sylius_seo_render_open_graph', $functions[0]->getName());
    }
}
