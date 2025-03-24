<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Action\SearchAction;

class Thing extends StructuredData
{
    public ?string $description = null;

    /** @var string|list<string>|null The URL of the item's image. This can be a URL or an array of URLs. */
    public string|array|null $image = null;

    public ?string $name = null;

    public ?SearchAction $potentialAction = null;

    public ?string $url = null;
}
