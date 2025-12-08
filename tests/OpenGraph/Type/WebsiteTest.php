<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Website;

final class WebsiteTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $website = new Website();

        self::assertSame('website', $website->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_properties(): void
    {
        $website = new Website();

        self::assertSame([], $website->getProperties());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('My Website')
            ->url('https://example.com')
            ->type(new Website());

        $data = $og->toArray();

        self::assertSame('My Website', $data['og:title']);
        self::assertSame('website', $data['og:type']);
        self::assertSame('https://example.com', $data['og:url']);
    }

    /**
     * @test
     */
    public function it_renders_website_meta_tags(): void
    {
        $og = (new OpenGraph())
            ->title('My Website')
            ->type(new Website());

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:type" content="website">', $html);
        self::assertStringContainsString('<meta property="og:title" content="My Website">', $html);
    }
}
