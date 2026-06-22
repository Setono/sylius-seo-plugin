<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags an indexable page that carries a `<meta name="robots" content="...noindex...">`.
 */
final class MetaRobotsNoindexDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'meta_robots_noindex';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $content = strtolower((string) $this->firstAttribute($crawler, 'meta[name="robots"]', 'content'));
        if (str_contains($content, 'noindex')) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Critical,
                'setono_sylius_seo.issue.meta_robots_noindex',
                [],
                ['content' => $content],
            );
        }
    }
}
