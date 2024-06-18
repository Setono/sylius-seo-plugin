<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData;

use Setono\SyliusSEOPlugin\LinkedData\DTO\LinkedData;

/**
 * @implements \IteratorAggregate<class-string<LinkedData>, LinkedData>
 */
final class LinkedDataContainer implements LinkedDataContainerInterface, \IteratorAggregate
{
    /** @var array<class-string<LinkedData>, LinkedData> */
    private array $data = [];

    /**
     * @psalm-assert-if-true LinkedData $this->data[$linkedData]
     */
    public function has(string $linkedData): bool
    {
        return isset($this->data[$linkedData]);
    }

    public function get(string $linkedData): LinkedData
    {
        if (!$this->has($linkedData)) {
            throw new \InvalidArgumentException(sprintf('Linked data with type "%s" does not exist', $linkedData));
        }

        return $this->data[$linkedData];
    }

    /**
     * @psalm-assert LinkedData $this->data[$linkedData]
     */
    public function set(LinkedData $linkedData): void
    {
        $this->data[$linkedData::class] = $linkedData;
    }

    /**
     * @return \ArrayIterator<class-string<LinkedData>, LinkedData>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }

    public function empty(): bool
    {
        return [] === $this->data;
    }

    public function count(): int
    {
        return count($this->data);
    }
}
