<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags `<img>` elements that have no `alt` attribute (bad for accessibility and image SEO).
 */
final class ImageAltDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'image_alt';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $count = $crawler->filter('img:not([alt])')->count();
        if ($count > 0) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Notice,
                'setono_sylius_seo.issue.image_alt',
                ['%count%' => $count],
                ['count' => $count],
            );
        }
    }
}
