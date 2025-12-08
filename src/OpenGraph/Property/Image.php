<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Property;

/**
 * Represents an Open Graph image with optional structured properties.
 *
 * @see https://ogp.me/#structured
 */
final class Image
{
    /**
     * @param string $url The URL of the image
     * @param string|null $secureUrl An HTTPS URL for the image
     * @param string|null $type The MIME type of the image (e.g., "image/jpeg")
     * @param int|null $width The width of the image in pixels
     * @param int|null $height The height of the image in pixels
     * @param string|null $alt A description of what is in the image (for accessibility)
     */
    public function __construct(
        public readonly string $url,
        public readonly ?string $secureUrl = null,
        public readonly ?string $type = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?string $alt = null,
    ) {
    }

    public static function create(string $url): self
    {
        return new self($url);
    }

    public function withSecureUrl(string $secureUrl): self
    {
        return new self($this->url, $secureUrl, $this->type, $this->width, $this->height, $this->alt);
    }

    public function withType(string $type): self
    {
        return new self($this->url, $this->secureUrl, $type, $this->width, $this->height, $this->alt);
    }

    public function withWidth(int $width): self
    {
        return new self($this->url, $this->secureUrl, $this->type, $width, $this->height, $this->alt);
    }

    public function withHeight(int $height): self
    {
        return new self($this->url, $this->secureUrl, $this->type, $this->width, $height, $this->alt);
    }

    public function withDimensions(int $width, int $height): self
    {
        return new self($this->url, $this->secureUrl, $this->type, $width, $height, $this->alt);
    }

    public function withAlt(string $alt): self
    {
        return new self($this->url, $this->secureUrl, $this->type, $this->width, $this->height, $alt);
    }

    /**
     * @return array<string, scalar>
     */
    public function toArray(): array
    {
        return array_filter([
            'og:image' => $this->url,
            'og:image:secure_url' => $this->secureUrl,
            'og:image:type' => $this->type,
            'og:image:width' => $this->width,
            'og:image:height' => $this->height,
            'og:image:alt' => $this->alt,
        ], static fn ($value) => null !== $value);
    }
}
