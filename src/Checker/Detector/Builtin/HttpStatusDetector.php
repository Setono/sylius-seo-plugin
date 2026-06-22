<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags pages that do not return HTTP 200 (broken links, server errors, unexpected redirects).
 */
final class HttpStatusDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'http_status_ok';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $statusCode = $inspection->statusCode;
        if (200 === $statusCode) {
            return;
        }

        $severity = match (true) {
            0 === $statusCode, $statusCode >= 500 => Severity::Critical,
            $statusCode >= 400 => Severity::Error,
            default => Severity::Warning,
        };

        yield new DetectedIssue(
            $this->getCode(),
            $severity,
            'setono_sylius_seo.issue.http_status_ok',
            ['%status%' => 0 === $statusCode ? 'unreachable' : (string) $statusCode],
            ['status' => $statusCode],
        );
    }
}
