<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\LinkedData\Thing\Intangible;

use Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\Brand;
use Setono\SyliusSEOPlugin\Tests\LinkedData\AbstractTestCase;

final class BrandTest extends AbstractTestCase
{
    protected function getObject(): object
    {
        return new Brand('Acme');
    }

    protected function getExpectedJson(): string
    {
        return <<<JSON
{
    "name": "Acme",
    "@type": "Brand"
}
JSON;
    }
}
