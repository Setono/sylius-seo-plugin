<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

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

    public function getProperties(): array
    {
        return array_filter([
            'profile:first_name' => $this->firstName,
            'profile:last_name' => $this->lastName,
            'profile:username' => $this->username,
            'profile:gender' => $this->gender,
        ], static fn ($value) => null !== $value);
    }
}
