<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Form\Type\Check;

use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\Assertion;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Shared choice lists for the configurable check form types.
 */
final class CheckConfigChoices
{
    /**
     * @return array<string, string>
     */
    public static function assertions(): array
    {
        $choices = [];
        foreach (Assertion::all() as $assertion) {
            $choices[ucfirst($assertion)] = $assertion;
        }

        return $choices;
    }

    /**
     * @return array<string, string>
     */
    public static function severities(): array
    {
        $choices = [];
        foreach (Severity::cases() as $severity) {
            $choices[ucfirst($severity->value)] = $severity->value;
        }

        return $choices;
    }
}
