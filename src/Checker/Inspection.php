<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * A neutral, immutable snapshot of one fetched URL handed to issue detectors.
 *
 * No SEO-specific parsing is baked in: detectors get the HTTP status, all response headers,
 * the raw body, and the lazily-parsed DOM, and parse anything domain-specific (JSON-LD, Open
 * Graph, ...) themselves. The DOM is only built on first access so header-only checks are cheap.
 */
final class Inspection
{
    private ?Crawler $crawler = null;

    private bool $crawlerInitialized = false;

    /**
     * @param array<string, list<string>> $headers lower-cased header name => list of values
     */
    public function __construct(
        public readonly PageInterface $page,
        public readonly string $url,
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly ?string $body,
        public readonly string $contentType = 'text/html',
    ) {
    }

    public function isHtml(): bool
    {
        return str_contains($this->contentType, 'html');
    }

    /**
     * The first value of the given (case-insensitive) response header, or null if absent.
     */
    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)][0] ?? null;
    }

    /**
     * All values of the given (case-insensitive) response header.
     *
     * @return list<string>
     */
    public function headerValues(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    /**
     * The parsed DOM, or null for empty/non-HTML bodies (so detectors can short-circuit cheaply).
     */
    public function getCrawler(): ?Crawler
    {
        if (!$this->crawlerInitialized) {
            $this->crawlerInitialized = true;

            if (null !== $this->body && '' !== $this->body && $this->isHtml()) {
                $this->crawler = new Crawler($this->body, $this->url);
            }
        }

        return $this->crawler;
    }
}
