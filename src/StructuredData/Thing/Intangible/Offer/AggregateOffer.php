<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer;

/**
 * @implements \ArrayAccess<int, Offer>
 */
class AggregateOffer extends Offer implements \Countable, \ArrayAccess
{
    public function __construct(
        /** @var list<Offer> */
        public array $offers = [],
    ) {
        parent::__construct();

        $this->type = 'AggregateOffer';
    }

    public function getLowPrice(): ?float
    {
        $lowestPrice = null;

        foreach ($this->offers as $offer) {
            if (null === $lowestPrice || $offer->price < $lowestPrice) {
                $lowestPrice = $offer->price;
            }
        }

        return $lowestPrice;
    }

    public function getHighPrice(): ?float
    {
        $highestPrice = null;

        foreach ($this->offers as $offer) {
            if (null === $highestPrice || $offer->price > $highestPrice) {
                $highestPrice = $offer->price;
            }
        }

        return $highestPrice;
    }

    public function getOfferCount(): int
    {
        return count($this->offers);
    }

    public function count(): int
    {
        return count($this->offers);
    }

    public function empty(): bool
    {
        return [] === $this->offers;
    }

    /**
     * @psalm-assert-if-true Offer $this->offers[$offset]
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->offers[$offset]);
    }

    public function offsetGet(mixed $offset): Offer
    {
        if (!$this->offsetExists($offset)) {
            throw new \OutOfBoundsException(sprintf('Offer with index "%s" does not exist', $offset));
        }

        return $this->offers[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            $this->offers[] = $value;

            return;
        }

        $offers = $this->offers;
        $offers[$offset] = $value;
        $this->offers = array_values($offers);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->offers[$offset]);
        $this->offers = array_values($this->offers);
    }
}
