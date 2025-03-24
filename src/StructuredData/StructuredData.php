<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData;

use Symfony\Component\Serializer\Attribute\SerializedName;

abstract class StructuredData
{
    #[SerializedName('@type')]
    public string $type;

    #[SerializedName('@context')]
    public ?string $context = null;

    #[SerializedName('@id')]
    public ?string $id = null;

    /**
     * @param array<string, mixed> $data
     */
    final public function __construct(array $data = [])
    {
        /** @var mixed $value */
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        $type = static::class;
        if (false !== $pos = strrpos($type, '\\')) {
            $type = substr($type, $pos + 1);
        }

        $this->type = $type;
    }
}
