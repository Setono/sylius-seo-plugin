<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

/**
 * Open Graph type for websites. This is the default type if none is specified.
 *
 * @see https://ogp.me/
 */
final class Website implements TypeInterface
{
    public function getType(): string
    {
        return 'website';
    }

    public function toHtml(): string
    {
        return '';
    }
}
