<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Serializer;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

interface StructuredDataSerializerInterface
{
    /**
     * Serializes the structured data to a JSON string
     *
     * @return string The JSON representation of the structured data
     */
    public function serialize(StructuredData $structuredData): string;
}
