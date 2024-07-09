<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\CreativeWork;

use Setono\SyliusSEOPlugin\LinkedData\LinkedData;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Action\SearchAction;

class WebSite extends LinkedData
{
    public function __construct(
        public ?string $url = null,
        public ?SearchAction $potentialAction = null,
    ) {
        $this->type = 'WebSite';
        $this->context = 'https://schema.org';
    }
}
