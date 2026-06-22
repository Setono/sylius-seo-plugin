<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Fetcher;

use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\PageInterface;

interface PageFetcherInterface
{
    /**
     * Fetches the given URL over HTTP and returns a neutral snapshot for the detectors. A transport
     * failure (e.g. unreachable host) is represented as an inspection with status code 0, not an
     * exception, so the "page is reachable" check can report it like any other issue.
     */
    public function fetch(PageInterface $page, string $url): Inspection;
}
