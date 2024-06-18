<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\LinkedData\DTO;

use Setono\SyliusSEOPlugin\LinkedData\DTO\Brand;

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
