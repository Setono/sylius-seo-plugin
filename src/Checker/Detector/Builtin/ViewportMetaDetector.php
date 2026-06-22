<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a page without a responsive `<meta name="viewport">`, which mobile-friendliness depends on.
 */
final class ViewportMetaDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'viewport_meta';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        if (0 === $crawler->filter('meta[name="viewport"]')->count()) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Notice,
                'setono_sylius_seo.issue.viewport_meta',
            );
        }
    }
}
