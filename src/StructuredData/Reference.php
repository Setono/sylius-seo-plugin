<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData;

use Symfony\Component\Serializer\Attribute\SerializedName;

final class Reference
{
    public function __construct(
        #[SerializedName('@id')]
        public string $id,
    ) {
    }
}
