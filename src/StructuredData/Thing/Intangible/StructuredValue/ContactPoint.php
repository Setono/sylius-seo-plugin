<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class ContactPoint extends StructuredData
{
    public function __construct(
        public ?string $email = null,
        public ?string $telephone = null,
    ) {
        $this->type = 'ContactPoint';
    }
}
