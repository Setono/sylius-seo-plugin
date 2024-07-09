<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible;

use Setono\SyliusSEOPlugin\LinkedData\StructuredData;

class MerchantReturnPolicy extends StructuredData
{
    public function __construct(
        public ?array $applicableCountry = null,
        public ?string $returnPolicyCountry = null,
        public ?string $returnPolicyCategory = null,
        public ?int $merchantReturnDays = null,
        public ?string $returnMethod = null,
        public ?string $returnFees = null,
        public ?string $refundType = null,
    ) {
        $this->type = 'MerchantReturnPolicy';
    }
}
