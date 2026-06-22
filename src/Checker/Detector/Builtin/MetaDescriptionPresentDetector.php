<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a page with a missing or empty `<meta name="description">`.
 */
final class MetaDescriptionPresentDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'meta_description_present';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $description = $this->firstAttribute($crawler, 'meta[name="description"]', 'content');
        if (null === $description || '' === trim($description)) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Warning,
                'setono_sylius_seo.issue.meta_description_present',
            );
        }
    }
}
