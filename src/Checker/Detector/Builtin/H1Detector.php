<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a page that does not have exactly one `<h1>` (zero or multiple).
 */
final class H1Detector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'h1_present';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $count = $crawler->filter('h1')->count();
        if (1 !== $count) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Warning,
                'setono_sylius_seo.issue.h1_present',
                ['%count%' => $count],
                ['count' => $count],
            );
        }
    }
}
