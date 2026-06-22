<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\ConfigurableIssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Form\Type\Check\HeaderConfigType;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * A backend-configurable check: asserts something about a response header (e.g. that
 * Strict-Transport-Security exists, or that Content-Type contains "text/html").
 */
final class HeaderDetector extends AbstractDetector implements ConfigurableIssueDetectorInterface
{
    public function getCode(): string
    {
        return 'header';
    }

    public function getConfigFormType(): string
    {
        return HeaderConfigType::class;
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $name = $this->configString($config, 'name');
        if (null === $name) {
            return;
        }

        $assertion = $this->configString($config, 'assertion') ?? Assertion::EXISTS;
        $expected = $this->configString($config, 'value');
        $actual = $inspection->header($name);

        if (Assertion::satisfied($assertion, $actual, $expected)) {
            return;
        }

        yield new DetectedIssue(
            $this->getCode(),
            $this->severityFromConfig($config, Severity::Warning),
            'setono_sylius_seo.issue.header',
            [
                '%name%' => $name,
                '%assertion%' => $assertion,
                '%value%' => $expected ?? '',
                '%actual%' => $actual ?? '',
            ],
            ['name' => $name, 'assertion' => $assertion, 'expected' => $expected, 'actual' => $actual],
        );
    }
}
