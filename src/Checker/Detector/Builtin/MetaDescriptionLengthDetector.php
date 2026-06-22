<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a `<meta name="description">` that is too short or too long for search result snippets.
 */
final class MetaDescriptionLengthDetector extends AbstractDetector
{
    private const MIN_LENGTH = 50;

    private const MAX_LENGTH = 160;

    public function getCode(): string
    {
        return 'meta_description_length';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $description = $this->firstAttribute($crawler, 'meta[name="description"]', 'content');
        if (null === $description || '' === trim($description)) {
            return;
        }

        $length = mb_strlen(trim($description));
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Notice,
                'setono_sylius_seo.issue.meta_description_length',
                ['%length%' => $length, '%min%' => self::MIN_LENGTH, '%max%' => self::MAX_LENGTH],
                ['length' => $length],
            );
        }
    }
}
