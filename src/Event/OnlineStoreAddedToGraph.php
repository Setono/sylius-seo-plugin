<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\OnlineStore;

final class OnlineStoreAddedToGraph
{
    public function __construct(public readonly OnlineStore $onlineStore)
    {
    }
}
