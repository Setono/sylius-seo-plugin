<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData;

use Setono\SyliusSEOPlugin\LinkedData\DTO\LinkedData;

/**
 * @implements LinkedDataContainerInterface<LinkedData>
 * @implements \IteratorAggregate<class-string<LinkedData>, list<LinkedData>>
 */
final class LinkedDataContainer implements LinkedDataContainerInterface, \IteratorAggregate
{
    /** @var array<class-string<LinkedData>, list<LinkedData>> */
    private array $data = [];

    /**
     * @psalm-assert-if-true non-empty-list<LinkedData> $this->data[$linkedData]
     */
    public function has(string $linkedData): bool
    {
        return isset($this->data[$linkedData]) && [] !== $this->data[$linkedData];
    }

    public function get(string $linkedData): array
    {
        return $this->data[$linkedData] ?? [];
    }

    public function set(LinkedData $linkedData, bool $append = false): void
    {
        $data = $append ? $this->get($linkedData::class) : [];
        $data[] = $linkedData;

        $this->data[$linkedData::class] = $data;
    }

    /**
     * @return \ArrayIterator<class-string<LinkedData>, list<LinkedData>>
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
        $c = 0;

        foreach ($this->data as $members) {
            $c += count($members);
        }

        return $c;
    }
}
