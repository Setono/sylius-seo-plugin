<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags an HTTPS page that loads sub-resources over plain HTTP (mixed content), which browsers
 * block or downgrade and which hurts trust signals.
 */
final class MixedContentDetector extends AbstractDetector
{
    private const SELECTORS = [
        'img[src^="http://"]',
        'script[src^="http://"]',
        'link[href^="http://"]',
        'iframe[src^="http://"]',
        'source[src^="http://"]',
    ];

    public function getCode(): string
    {
        return 'mixed_content';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        if (!str_starts_with($inspection->url, 'https://')) {
            return;
        }

        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $count = 0;
        foreach (self::SELECTORS as $selector) {
            $count += $crawler->filter($selector)->count();
        }

        if ($count > 0) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Warning,
                'setono_sylius_seo.issue.mixed_content',
                ['%count%' => $count],
                ['count' => $count],
            );
        }
    }
}
