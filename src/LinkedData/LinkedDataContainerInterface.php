<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData;

use Setono\SyliusSEOPlugin\LinkedData\DTO\LinkedData;

/**
 * @template T of LinkedData
 *
 * @extends \Traversable<class-string<T>, list<T>>
 */
interface LinkedDataContainerInterface extends \Traversable, \Countable
{
    /**
     * @param class-string<LinkedData> $linkedData
     */
    public function has(string $linkedData): bool;

    /**
     * @param class-string<T> $linkedData
     *
     * @return list<T>
     */
    public function get(string $linkedData): array;

    /**
     * @param bool $append If true, the linked data will be appended to the container, otherwise it will overwrite any existing linked data
     */
    public function set(LinkedData $linkedData, bool $append = false): void;

    /**
     * Returns true if the container is empty
     */
    public function empty(): bool;
}
