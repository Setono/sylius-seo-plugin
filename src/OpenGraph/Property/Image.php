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
    private ?string $secureUrl = null;

    private ?string $type = null;

    private ?int $width = null;

    private ?int $height = null;

    private ?string $alt = null;

    public function __construct(private string $url)
    {
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function secureUrl(?string $secureUrl): self
    {
        $this->secureUrl = $secureUrl;

        return $this;
    }

    public function getSecureUrl(): ?string
    {
        return $this->secureUrl;
    }

    public function type(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function width(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function height(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function dimensions(?int $width, ?int $height): self
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function alt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
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
