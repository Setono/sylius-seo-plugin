<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\LinkedData\DTO;

use Setono\SyliusSEOPlugin\LinkedData\DTO\Offer;

final class OfferTest extends AbstractTestCase
{
    private string $date = '2020-01-01 00:00:00';

    protected function getObject(): object
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
