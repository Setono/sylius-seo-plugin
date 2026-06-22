<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector;

interface DetectorRegistryInterface
{
    public function get(string $code): ?IssueDetectorInterface;

    public function has(string $code): bool;

    /**
     * @return array<string, IssueDetectorInterface> indexed by check code
     */
    public function all(): array;
}
