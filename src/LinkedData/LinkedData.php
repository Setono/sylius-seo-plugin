<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData;

use Symfony\Component\Serializer\Attribute\SerializedName;

// todo should be renamed to SchemaData or StructuredData. It's not linked data, it is structured data
abstract class LinkedData
{
    // todo set the type to the class name by default
    #[SerializedName('@type')]
    public string $type;

    #[SerializedName('@context')]
    public ?string $context = null;
}
