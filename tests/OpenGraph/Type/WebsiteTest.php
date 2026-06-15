<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Website;

final class WebsiteTest extends TestCase
{
    #[Test]
    public function it_returns_correct_type(): void
    {
        $website = new Website();

        self::assertSame('website', $website->getType());
    }

    #[Test]
    public function it_returns_empty_html(): void
    {
        $website = new Website();

        self::assertSame('', $website->toHtml());
    }

    #[Test]
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('My Website')
            ->url('https://example.com')
            ->type(new Website());

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="My Website">', $html);
        self::assertStringContainsString('<meta property="og:type" content="website">', $html);
        self::assertStringContainsString('<meta property="og:url" content="https://example.com">', $html);
    }
}
