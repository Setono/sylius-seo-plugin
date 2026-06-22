<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags `<script type="application/ld+json">` blocks whose contents are not valid JSON, which makes
 * the structured data unusable for rich results.
 */
final class JsonLdValidDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'json_ld_valid';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $index = 0;
        foreach ($crawler->filter('script[type="application/ld+json"]') as $node) {
            ++$index;

            $content = trim($node->textContent);
            if ('' === $content) {
                continue;
            }

            json_decode($content, true);
            if (\JSON_ERROR_NONE !== json_last_error()) {
                yield new DetectedIssue(
                    $this->getCode(),
                    Severity::Warning,
                    'setono_sylius_seo.issue.json_ld_valid',
                    ['%error%' => json_last_error_msg()],
                    ['error' => json_last_error_msg(), 'index' => $index],
                    (string) $index,
                );
            }
        }
    }
}
