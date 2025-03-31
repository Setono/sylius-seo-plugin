<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\WebSite;

final class WebsiteAddedToGraph
{
    public function __construct(public readonly WebSite $webSite)
    {
    }
}
