<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\OnlineStore;

use Setono\SyliusSEOPlugin\LinkedData\Thing\Organization\OnlineBusiness\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;

interface OnlineStoreDataMapperInterface
{
    /**
     * Maps a channel to an online store
     */
    public function map(ChannelInterface $channel, OnlineStore $onlineStore): void;
}
