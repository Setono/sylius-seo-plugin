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
     * Render the type-specific properties as HTML meta tags.
     */
    public function toHtml(): string;
}
