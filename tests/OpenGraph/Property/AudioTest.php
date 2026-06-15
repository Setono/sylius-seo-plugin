<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Property;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Audio;

final class AudioTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_audio_with_url(): void
    {
        $audio = new Audio('https://example.com/audio.mp3');

        self::assertSame('https://example.com/audio.mp3', $audio->getUrl());
        self::assertNull($audio->getSecureUrl());
        self::assertNull($audio->getType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_url(): void
    {
        $audio = new Audio('https://example.com/audio.mp3');
        $audio->url('https://example.com/other.mp3');

        self::assertSame('https://example.com/other.mp3', $audio->getUrl());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_secure_url(): void
    {
        $audio = new Audio('https://example.com/audio.mp3');
        $audio->secureUrl('https://secure.example.com/audio.mp3');

        self::assertSame('https://secure.example.com/audio.mp3', $audio->getSecureUrl());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_type(): void
    {
        $audio = new Audio('https://example.com/audio.mp3');
        $audio->type('audio/mpeg');

        self::assertSame('audio/mpeg', $audio->getType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_supports_fluent_chaining(): void
    {
        $audio = (new Audio('https://example.com/audio.mp3'))
            ->secureUrl('https://secure.example.com/audio.mp3')
            ->type('audio/mpeg');

        self::assertSame('https://example.com/audio.mp3', $audio->getUrl());
        self::assertSame('https://secure.example.com/audio.mp3', $audio->getSecureUrl());
        self::assertSame('audio/mpeg', $audio->getType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_html_with_only_url(): void
    {
        $audio = new Audio('https://example.com/audio.mp3');

        self::assertSame('<meta property="og:audio" content="https://example.com/audio.mp3">', $audio->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_html_with_all_properties(): void
    {
        $audio = (new Audio('https://example.com/audio.mp3'))
            ->secureUrl('https://secure.example.com/audio.mp3')
            ->type('audio/mpeg');

        $expected = '<meta property="og:audio" content="https://example.com/audio.mp3">' . "\n" .
            '<meta property="og:audio:secure_url" content="https://secure.example.com/audio.mp3">' . "\n" .
            '<meta property="og:audio:type" content="audio/mpeg">';

        self::assertSame($expected, $audio->toHtml());
    }
}
