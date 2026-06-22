<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a page without a `<link rel="canonical">`, or with an empty/relative canonical href.
 */
final class CanonicalPresentDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'canonical_present';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $href = $this->firstAttribute($crawler, 'link[rel="canonical"]', 'href');
        if (null === $href || '' === trim($href)) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Warning,
                'setono_sylius_seo.issue.canonical_present',
            );

            return;
        }

        if (!str_starts_with($href, 'http://') && !str_starts_with($href, 'https://')) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Warning,
                'setono_sylius_seo.issue.canonical_relative',
                ['%href%' => $href],
                ['href' => $href],
            );
        }
    }
}
