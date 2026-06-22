<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Repository;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

interface PageRepositoryInterface
{
    public function findOneById(int $id): ?PageInterface;

    /**
     * @return list<PageInterface>
     */
    public function findEnabled(?ChannelInterface $channel = null): array;
}
