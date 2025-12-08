<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Property;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Audio;

final class AudioTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_audio_with_url(): void
    {
        $audio = new Audio('https://example.com/audio.mp3');

        self::assertSame('https://example.com/audio.mp3', $audio->url);
        self::assertNull($audio->secureUrl);
        self::assertNull($audio->type);
    }

    /**
     * @test
     */
    public function it_creates_audio_using_static_factory(): void
    {
        $audio = Audio::create('https://example.com/audio.mp3');

        self::assertSame('https://example.com/audio.mp3', $audio->url);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_secure_url(): void
    {
        $original = new Audio('https://example.com/audio.mp3');
        $modified = $original->withSecureUrl('https://secure.example.com/audio.mp3');

        self::assertNotSame($original, $modified);
        self::assertNull($original->secureUrl);
        self::assertSame('https://secure.example.com/audio.mp3', $modified->secureUrl);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_type(): void
    {
        $original = new Audio('https://example.com/audio.mp3');
        $modified = $original->withType('audio/mpeg');

        self::assertNotSame($original, $modified);
        self::assertNull($original->type);
        self::assertSame('audio/mpeg', $modified->type);
    }

    /**
     * @test
     */
    public function it_supports_fluent_chaining(): void
    {
        $audio = Audio::create('https://example.com/audio.mp3')
            ->withSecureUrl('https://secure.example.com/audio.mp3')
            ->withType('audio/mpeg');

        self::assertSame('https://example.com/audio.mp3', $audio->url);
        self::assertSame('https://secure.example.com/audio.mp3', $audio->secureUrl);
        self::assertSame('audio/mpeg', $audio->type);
    }

    /**
     * @test
     */
    public function it_converts_to_array_with_only_url(): void
    {
        $audio = new Audio('https://example.com/audio.mp3');

        self::assertSame(['og:audio' => 'https://example.com/audio.mp3'], $audio->toArray());
    }

    /**
     * @test
     */
    public function it_converts_to_array_with_all_properties(): void
    {
        $audio = new Audio(
            'https://example.com/audio.mp3',
            'https://secure.example.com/audio.mp3',
            'audio/mpeg',
        );

        $expected = [
            'og:audio' => 'https://example.com/audio.mp3',
            'og:audio:secure_url' => 'https://secure.example.com/audio.mp3',
            'og:audio:type' => 'audio/mpeg',
        ];

        self::assertSame($expected, $audio->toArray());
    }
}
