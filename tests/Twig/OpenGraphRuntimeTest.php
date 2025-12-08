<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Website;
use Setono\SyliusSEOPlugin\Twig\OpenGraphRuntime;

final class OpenGraphRuntimeTest extends TestCase
{
    /**
     * @test
     */
    public function it_renders_open_graph(): void
    {
        $openGraph = (new OpenGraph())
            ->title('Test Page')
            ->type(new Website());

        $runtime = new OpenGraphRuntime($openGraph);
        $html = $runtime->renderOpenGraph();

        self::assertStringContainsString('<meta property="og:title" content="Test Page">', $html);
        self::assertStringContainsString('<meta property="og:type" content="website">', $html);
    }

    /**
     * @test
     */
    public function it_renders_empty_string_when_no_data(): void
    {
        $openGraph = new OpenGraph();
        $runtime = new OpenGraphRuntime($openGraph);

        self::assertSame('', $runtime->renderOpenGraph());
    }
}
