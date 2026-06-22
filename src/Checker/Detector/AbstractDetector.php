<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector;

use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractDetector implements IssueDetectorInterface
{
    /**
     * The parsed DOM, but only for a successfully rendered HTML page (2xx). Detectors that inspect
     * markup should start with `if (null === $crawler = $this->htmlCrawler($inspection)) { return; }`
     * so they stay silent on redirects, errors and non-HTML responses (other checks cover those).
     */
    protected function htmlCrawler(Inspection $inspection): ?Crawler
    {
        if (!$inspection->isHtml() || $inspection->statusCode < 200 || $inspection->statusCode >= 300) {
            return null;
        }

        return $inspection->getCrawler();
    }

    /**
     * The trimmed text of the first node matching the CSS selector, or null when there is no match.
     */
    protected function firstText(Crawler $crawler, string $selector): ?string
    {
        $node = $crawler->filter($selector);

        return $node->count() > 0 ? trim($node->first()->text('')) : null;
    }

    /**
     * The given attribute of the first node matching the CSS selector, or null.
     */
    protected function firstAttribute(Crawler $crawler, string $selector, string $attribute): ?string
    {
        $node = $crawler->filter($selector);

        return $node->count() > 0 ? $node->first()->attr($attribute) : null;
    }

    /**
     * A non-empty string configuration value, or null.
     *
     * @param array<string, mixed> $config
     */
    protected function configString(array $config, string $key): ?string
    {
        $value = $config[$key] ?? null;

        return \is_string($value) && '' !== $value ? $value : null;
    }

    /**
     * An integer configuration value, or null.
     *
     * @param array<string, mixed> $config
     */
    protected function configInt(array $config, string $key): ?int
    {
        $value = $config[$key] ?? null;
        if (\is_int($value)) {
            return $value;
        }

        if (\is_string($value) && '' !== $value && ctype_digit(ltrim($value, '-'))) {
            return (int) $value;
        }

        return null;
    }

    /**
     * The severity configured for a parametric check, falling back to the given default.
     *
     * @param array<string, mixed> $config
     */
    protected function severityFromConfig(array $config, Severity $default): Severity
    {
        $value = $this->configString($config, 'severity');

        return null !== $value ? (Severity::tryFrom($value) ?? $default) : $default;
    }
}
