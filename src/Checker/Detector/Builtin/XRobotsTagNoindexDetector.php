<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags an `X-Robots-Tag: ...noindex...` response header, which removes the page from search
 * results just like a meta robots tag, but is easy to miss because it is not in the markup.
 */
final class XRobotsTagNoindexDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'x_robots_tag_noindex';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        if ($inspection->statusCode < 200 || $inspection->statusCode >= 300) {
            return;
        }

        foreach ($inspection->headerValues('x-robots-tag') as $value) {
            if (str_contains(strtolower($value), 'noindex')) {
                yield new DetectedIssue(
                    $this->getCode(),
                    Severity::Critical,
                    'setono_sylius_seo.issue.x_robots_tag_noindex',
                    [],
                    ['header' => $value],
                );

                return;
            }
        }
    }
}
