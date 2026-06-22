<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Inspection;

/**
 * A check. Implement this interface and tag the service with `setono_sylius_seo.issue_detector`
 * (or rely on autoconfiguration) to add a new check that operators can assign to their pages.
 *
 * Detectors must be side-effect free and must NOT throw on malformed pages (return/yield nothing
 * instead). The runner catches throwables so one broken check never hides the rest, but failing
 * soft keeps the behaviour predictable.
 */
interface IssueDetectorInterface
{
    /**
     * A stable, unique machine identifier for this check (e.g. "title_length"). Used to assign the
     * check to a page and to group/filter the resulting issues.
     */
    public function getCode(): string;

    /**
     * @param array<string, mixed> $config the per-assignment configuration (empty for zero-config checks)
     *
     * @return iterable<DetectedIssue>
     */
    public function detect(Inspection $inspection, array $config = []): iterable;
}
