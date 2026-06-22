<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Repository;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of PageInterface
 * @extends RepositoryInterface<T>
 */
interface PageRepositoryInterface extends RepositoryInterface
{
    /**
     * @return list<PageInterface>
     */
    public function findEnabled(?ChannelInterface $channel = null): array;
}
