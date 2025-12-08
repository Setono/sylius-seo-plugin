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
    private ?string $secureUrl = null;

    private ?string $type = null;

    private ?int $width = null;

    private ?int $height = null;

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
