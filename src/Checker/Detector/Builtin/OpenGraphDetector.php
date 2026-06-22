<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a page missing the core Open Graph tags (og:title, og:type, og:image) used when the page
 * is shared on social platforms.
 */
final class OpenGraphDetector extends AbstractDetector
{
    private const REQUIRED_PROPERTIES = ['og:title', 'og:type', 'og:image'];

    public function getCode(): string
    {
        return 'open_graph_present';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $missing = [];
        foreach (self::REQUIRED_PROPERTIES as $property) {
            $content = $this->firstAttribute($crawler, sprintf('meta[property="%s"]', $property), 'content');
            if (null === $content || '' === trim($content)) {
                $missing[] = $property;
            }
        }

        if ([] !== $missing) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Notice,
                'setono_sylius_seo.issue.open_graph_present',
                ['%properties%' => implode(', ', $missing)],
                ['missing' => $missing],
            );
        }
    }
}
