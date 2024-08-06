<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin;

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
