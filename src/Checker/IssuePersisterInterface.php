<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker;

use Setono\SyliusSEOPlugin\Model\PageInterface;

interface IssuePersisterInterface
{
    /**
     * Upserts a detected issue by its fingerprint and returns that fingerprint.
     *
     * @param array{id: string, code: string, config: array<string, mixed>} $assignment
     */
    public function upsert(PageInterface $page, string $url, array $assignment, DetectedIssue $detectedIssue): string;

    /**
     * Marks the page's open issues that were NOT seen in the current run as resolved. Ignored
     * issues are left untouched.
     *
     * @param list<string> $seenFingerprints
     */
    public function resolveMissing(PageInterface $page, array $seenFingerprints): void;
}
