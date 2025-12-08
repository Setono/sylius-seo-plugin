<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Property;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;

/**
 * Represents an Open Graph audio with optional structured properties.
 *
 * @see https://ogp.me/#structured
 */
final class Audio
{
    private ?string $secureUrl = null;

    private ?string $type = null;

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

    public function toHtml(): string
    {
        return implode("\n", array_filter([
            OpenGraph::renderMetaTag('og:audio', $this->url),
            OpenGraph::renderMetaTag('og:audio:secure_url', $this->secureUrl),
            OpenGraph::renderMetaTag('og:audio:type', $this->type),
        ], static fn ($value): bool => $value !== null));
    }
}
