<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;

/**
 * Open Graph type for representing a person.
 *
 * @see https://ogp.me/#type_profile
 */
final class Profile implements TypeInterface
{
    public function __construct(
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $username = null,
        public readonly ?string $gender = null,
    ) {
    }

    public function getType(): string
    {
        return 'profile';
    }

    public function toHtml(): string
    {
        return implode("\n", array_filter([
            OpenGraph::renderMetaTag('profile:first_name', $this->firstName),
            OpenGraph::renderMetaTag('profile:last_name', $this->lastName),
            OpenGraph::renderMetaTag('profile:username', $this->username),
            OpenGraph::renderMetaTag('profile:gender', $this->gender),
        ], static fn ($value): bool => $value !== null));
    }
}
