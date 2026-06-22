<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\ConfigurableIssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Form\Type\Check\StatusCodeConfigType;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * A backend-configurable check: asserts that the page returns a specific HTTP status code (e.g. a
 * URL that should redirect, or one that should return 404).
 */
final class StatusCodeDetector extends AbstractDetector implements ConfigurableIssueDetectorInterface
{
    public function getCode(): string
    {
        return 'status_code';
    }

    public function getConfigFormType(): string
    {
        return StatusCodeConfigType::class;
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $expected = $this->configInt($config, 'expected') ?? 200;

        if ($inspection->statusCode === $expected) {
            return;
        }

        yield new DetectedIssue(
            $this->getCode(),
            $this->severityFromConfig($config, Severity::Error),
            'setono_sylius_seo.issue.status_code',
            ['%expected%' => $expected, '%actual%' => $inspection->statusCode],
            ['expected' => $expected, 'actual' => $inspection->statusCode],
        );
    }
}
