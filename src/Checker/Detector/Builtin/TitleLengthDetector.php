<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a `<title>` that is too short or too long to display well in search results.
 */
final class TitleLengthDetector extends AbstractDetector
{
    private const MIN_LENGTH = 10;

    private const MAX_LENGTH = 60;

    public function getCode(): string
    {
        return 'title_length';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $title = $this->firstText($crawler, 'head > title');
        if (null === $title || '' === $title) {
            return;
        }

        $length = mb_strlen($title);
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Warning,
                'setono_sylius_seo.issue.title_length',
                ['%length%' => $length, '%min%' => self::MIN_LENGTH, '%max%' => self::MAX_LENGTH],
                ['title' => $title, 'length' => $length],
            );
        }
    }
}
