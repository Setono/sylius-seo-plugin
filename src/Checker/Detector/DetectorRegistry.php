<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector;

use Webmozart\Assert\Assert;

/**
 * Collects all tagged detectors and indexes them by their code, so the admin can list the
 * available checks and the runner can execute only the checks a page selected.
 */
final class DetectorRegistry implements DetectorRegistryInterface
{
    /** @var array<string, IssueDetectorInterface> */
    private array $detectors = [];

    /**
     * @param iterable<IssueDetectorInterface> $detectors
     */
    public function __construct(iterable $detectors)
    {
        foreach ($detectors as $detector) {
            $code = $detector->getCode();
            Assert::keyNotExists(
                $this->detectors,
                $code,
                sprintf('Two issue detectors share the code "%s". Codes must be unique.', $code),
            );

            $this->detectors[$code] = $detector;
        }
    }

    public function get(string $code): ?IssueDetectorInterface
    {
        return $this->detectors[$code] ?? null;
    }

    public function has(string $code): bool
    {
        return isset($this->detectors[$code]);
    }

    public function all(): array
    {
        return $this->detectors;
    }
}
