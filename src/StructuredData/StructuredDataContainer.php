<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData;

/**
 * @implements StructuredDataContainerInterface<StructuredData>
 * @implements \IteratorAggregate<class-string<StructuredData>, list<StructuredData>>
 */
final class StructuredDataContainer implements StructuredDataContainerInterface, \IteratorAggregate
{
    /** @var array<class-string<StructuredData>, list<StructuredData>> */
    private array $data = [];

    /**
     * @psalm-assert-if-true non-empty-list<StructuredData> $this->data[$linkedData]
     */
    public function has(string $linkedData): bool
    {
        return isset($this->data[$linkedData]) && [] !== $this->data[$linkedData];
    }

    public function get(string $linkedData): array
    {
        return $this->data[$linkedData] ?? [];
    }

    public function set(StructuredData $linkedData, bool $append = false): void
    {
        $data = $append ? $this->get($linkedData::class) : [];
        $data[] = $linkedData;

        $this->data[$linkedData::class] = $data;
    }

    /**
     * @return \ArrayIterator<class-string<StructuredData>, list<StructuredData>>
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
