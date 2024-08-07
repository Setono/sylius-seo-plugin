<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData;

/**
 * @template T of StructuredData
 *
 * @extends \Traversable<class-string<T>, list<T>>
 */
interface StructuredDataContainerInterface extends \Traversable, \Countable
{
    /**
     * @param class-string<StructuredData> $linkedData
     */
    public function has(string $linkedData): bool;

    /**
     * @param class-string<T> $linkedData
     *
     * @return list<T>
     */
    public function get(string $linkedData): array;

    /**
     * @param bool $append If true, the linked data will be appended to the container (the key used on the container is the FQCN), otherwise it will overwrite any existing linked data
     */
    public function set(StructuredData $linkedData, bool $append = false): void;

    /**
     * Returns true if the container is empty
     */
    public function empty(): bool;
}
