<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData;

use Setono\SyliusSEOPlugin\LinkedData\DTO\LinkedData;

/**
 * @extends \Traversable<string, LinkedData>
 */
interface LinkedDataContainerInterface extends \Traversable, \Countable
{
    /**
     * @param class-string<LinkedData> $linkedData
     */
    public function has(string $linkedData): bool;

    /**
     * If the $linkedData does not exist already, it will be created and returned, so you can do:
     *
     * $linkedDataContainer->get(Product::class)->sku = 'BLUE_T_SHIRT_123';
     *
     * @template T of LinkedData
     *
     * @param class-string<T> $linkedData
     *
     * @throws \InvalidArgumentException if the linked data does not exist
     *
     * @return T
     */
    public function get(string $linkedData): LinkedData;

    public function set(LinkedData $linkedData): void;

    /**
     * Returns true if the container is empty
     */
    public function empty(): bool;
}
