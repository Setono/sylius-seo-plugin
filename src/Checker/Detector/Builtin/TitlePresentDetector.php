<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a page with a missing or empty `<title>`.
 */
final class TitlePresentDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'title_present';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        if (null === $this->firstText($crawler, 'head > title') || '' === $this->firstText($crawler, 'head > title')) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Error,
                'setono_sylius_seo.issue.title_present',
            );
        }
    }
}
