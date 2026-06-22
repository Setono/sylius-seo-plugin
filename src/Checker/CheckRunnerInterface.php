<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

interface CheckRunnerInterface
{
    /**
     * Resolves and fetches the page's URL, runs its assigned checks, and persists the issues.
     */
    public function run(PageInterface $page): void;

    /**
     * Runs every enabled page, optionally limited to a single channel.
     */
    public function runAll(?ChannelInterface $channel = null): void;
}
