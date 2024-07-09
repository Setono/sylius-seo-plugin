<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\LinkedData\LinkedData;

class ContactPoint extends LinkedData
{
    public function __construct(
        public ?string $email = null,
        public ?string $telephone = null,
    ) {
        $this->type = 'ContactPoint';
    }
}
