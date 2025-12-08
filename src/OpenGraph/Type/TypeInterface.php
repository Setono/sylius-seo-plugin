<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

interface TypeInterface
{
    /**
     * Returns the Open Graph type value (e.g., "article", "profile", "video.movie").
     */
    public function getType(): string;

    /**
     * Returns type-specific Open Graph properties.
     *
     * @return array<string, scalar|list<scalar>>
     */
    public function getProperties(): array;
}
