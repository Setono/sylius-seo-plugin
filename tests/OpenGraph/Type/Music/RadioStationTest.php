<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Music;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Music\RadioStation;

final class RadioStationTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_correct_type(): void
    {
        $radioStation = new RadioStation();

        self::assertSame('music.radio_station', $radioStation->getType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_html_by_default(): void
    {
        $radioStation = new RadioStation();

        self::assertSame('', $radioStation->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_creators(): void
    {
        $radioStation = new RadioStation(creators: [
            'https://example.com/profile/1',
            'https://example.com/profile/2',
        ]);

        $expected = '<meta property="music:creator" content="https://example.com/profile/1">' . "\n" .
            '<meta property="music:creator" content="https://example.com/profile/2">';

        self::assertSame($expected, $radioStation->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('Cool FM')
            ->type(new RadioStation(creators: ['https://example.com/profile/1']));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="Cool FM">', $html);
        self::assertStringContainsString('<meta property="og:type" content="music.radio_station">', $html);
        self::assertStringContainsString('<meta property="music:creator" content="https://example.com/profile/1">', $html);
    }
}
