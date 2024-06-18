<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\UrlGenerator;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductVariantUrlGeneratorInterface
{
    /**
     * Generates the URL for the given product variant
     */
    public function generate(ProductVariantInterface $productVariant): string;
}
