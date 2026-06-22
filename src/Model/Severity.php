<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

enum Severity: string
{
    /** Informational / cosmetic */
    case Notice = 'notice';

    /** Should fix; minor ranking impact */
    case Warning = 'warning';

    /** A real SEO defect */
    case Error = 'error';

    /** Page is broken for search engines (e.g. 5xx, noindex on an indexable page) */
    case Critical = 'critical';

    /**
     * A higher weight means a more severe issue. Useful for sorting.
     */
    public function weight(): int
    {
        return match ($this) {
            self::Notice => 10,
            self::Warning => 20,
            self::Error => 30,
            self::Critical => 40,
        };
    }
}
