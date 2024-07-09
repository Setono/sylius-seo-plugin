<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Website;

use Setono\SyliusSEOPlugin\LinkedData\Thing\CreativeWork\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;

interface WebsiteDataMapperInterface
{
    /**
     * Maps a channel to a website
     */
    public function map(ChannelInterface $channel, WebSite $webSite): void;
}
