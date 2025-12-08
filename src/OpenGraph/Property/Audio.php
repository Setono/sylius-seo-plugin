<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Property;

/**
 * Represents an Open Graph audio with optional structured properties.
 *
 * @see https://ogp.me/#structured
 */
final class Audio
{
    /**
     * @param string $url The URL of the audio file
     * @param string|null $secureUrl An HTTPS URL for the audio file
     * @param string|null $type The MIME type of the audio file (e.g., "audio/mpeg")
     */
    public function __construct(
        public readonly string $url,
        public readonly ?string $secureUrl = null,
        public readonly ?string $type = null,
    ) {
    }

    public static function create(string $url): self
    {
        return new self($url);
    }

    public function withSecureUrl(string $secureUrl): self
    {
        return new self($this->url, $secureUrl, $this->type);
    }

    public function withType(string $type): self
    {
        return new self($this->url, $this->secureUrl, $type);
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return array_filter([
            'og:audio' => $this->url,
            'og:audio:secure_url' => $this->secureUrl,
            'og:audio:type' => $this->type,
        ], static fn ($value) => null !== $value);
    }
}
