<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing;

use Setono\SyliusSEOPlugin\StructuredData\Thing;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\EntryPoint;

class Action extends Thing
{
    public ?EntryPoint $target = null;
}
