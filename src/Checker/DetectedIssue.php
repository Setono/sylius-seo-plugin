<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker;

use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * The immutable result of a detector finding a problem. Distinct from the persisted
 * {@see \Setono\SyliusSEOPlugin\Model\Issue} entity: the persister maps this onto a row,
 * computing the fingerprint from the page, the check assignment and the optional discriminator.
 */
final class DetectedIssue
{
    /**
     * @param string $check the detector code (e.g. "title_length")
     * @param string $messageTemplate a translation key
     * @param array<string, scalar|null> $messageParameters parameters for the translation
     * @param array<string, mixed> $context arbitrary metadata for display (offending value, etc.)
     * @param string|null $discriminator distinguishes multiple issues of the same check on one page
     */
    public function __construct(
        public readonly string $check,
        public readonly Severity $severity,
        public readonly string $messageTemplate,
        public readonly array $messageParameters = [],
        public readonly array $context = [],
        public readonly ?string $discriminator = null,
    ) {
    }
}
