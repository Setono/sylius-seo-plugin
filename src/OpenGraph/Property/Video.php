<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Property;

/**
 * Represents an Open Graph video with optional structured properties.
 *
 * @see https://ogp.me/#structured
 */
final class Video
{
    /**
     * @param string $url The URL of the video
     * @param string|null $secureUrl An HTTPS URL for the video
     * @param string|null $type The MIME type of the video (e.g., "video/mp4")
     * @param int|null $width The width of the video in pixels
     * @param int|null $height The height of the video in pixels
     */
    public function __construct(
        public readonly string $url,
        public readonly ?string $secureUrl = null,
        public readonly ?string $type = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {
    }

    public static function create(string $url): self
    {
        return new self($url);
    }

    public function withSecureUrl(string $secureUrl): self
    {
        return new self($this->url, $secureUrl, $this->type, $this->width, $this->height);
    }

    public function withType(string $type): self
    {
        return new self($this->url, $this->secureUrl, $type, $this->width, $this->height);
    }

    public function withWidth(int $width): self
    {
        return new self($this->url, $this->secureUrl, $this->type, $width, $this->height);
    }

    public function withHeight(int $height): self
    {
        return new self($this->url, $this->secureUrl, $this->type, $this->width, $height);
    }

    public function withDimensions(int $width, int $height): self
    {
        return new self($this->url, $this->secureUrl, $this->type, $width, $height);
    }

    /**
     * @return array<string, scalar>
     */
    public function toArray(): array
    {
        return array_filter([
            'og:video' => $this->url,
            'og:video:secure_url' => $this->secureUrl,
            'og:video:type' => $this->type,
            'og:video:width' => $this->width,
            'og:video:height' => $this->height,
        ], static fn ($value) => null !== $value);
    }
}
