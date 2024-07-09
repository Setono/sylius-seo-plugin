<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer;
use Setono\SyliusSEOPlugin\Tests\StructuredData\AbstractTestCase;

final class OfferTest extends AbstractTestCase
{
    private string $date = '2020-01-01 00:00:00';

    protected function getObject(): Offer
    {
        return new Offer(
            url: 'https://example.com',
            priceCurrency: 'USD',
            price: 100.25,
            priceValidUntil: self::getDate($this->date),
        );
    }

    protected function getExpectedJson(): string
    {
        return <<<JSON
{
    "url": "https://example.com",
    "priceCurrency": "USD",
    "price": 100.25,
    "priceValidUntil": "{$this->date}",
    "@type": "Offer"
}
JSON;
    }
}
