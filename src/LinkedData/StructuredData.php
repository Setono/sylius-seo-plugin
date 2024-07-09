<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData;

use Symfony\Component\Serializer\Attribute\SerializedName;

abstract class StructuredData
{
    // todo set the type to the class name by default
    #[SerializedName('@type')]
    public string $type;

    #[SerializedName('@context')]
    public ?string $context = null;
}
