<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin;

use function Symfony\Component\String\u;

/** @psalm-suppress UndefinedClass,MixedArgument */
if (!\function_exists(formatAmount::class)) {
    function formatAmount(?int $amount): float
    {
        if (null === $amount) {
            return 0.0;
        }

        return round($amount / 100, 2);
    }
}

/** @psalm-suppress UndefinedClass,MixedArgument */
if (!\function_exists(sanitizeString::class)) {
    function sanitizeString(?string $string, bool $stripTags = true, int $maxLength = null): ?string
    {
        if (null === $string) {
            return null;
        }

        if ($stripTags) {
            $string = strip_tags($string);
        }

        $s = u($string)->replaceMatches('/\s+/', ' ')->trim();
        if (null !== $maxLength) {
            $s = $s->truncate($maxLength);
        }

        return $s->toString();
    }
}
