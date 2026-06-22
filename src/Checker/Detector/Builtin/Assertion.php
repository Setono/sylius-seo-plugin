<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

/**
 * The assertions a configurable check can make about an extracted value. Shared by the
 * element-content and header checks.
 */
final class Assertion
{
    public const EXISTS = 'exists';

    public const ABSENT = 'absent';

    public const CONTAINS = 'contains';

    public const EQUALS = 'equals';

    public const MATCHES = 'matches';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [self::EXISTS, self::ABSENT, self::CONTAINS, self::EQUALS, self::MATCHES];
    }

    /**
     * Whether the actual value satisfies the assertion (i.e. there is NO issue).
     */
    public static function satisfied(string $assertion, ?string $actual, ?string $expected): bool
    {
        return match ($assertion) {
            self::EXISTS => null !== $actual,
            self::ABSENT => null === $actual,
            self::CONTAINS => null !== $actual && null !== $expected && str_contains($actual, $expected),
            self::EQUALS => $actual === $expected,
            self::MATCHES => null !== $actual && null !== $expected && 1 === @preg_match($expected, $actual),
            default => true,
        };
    }
}
