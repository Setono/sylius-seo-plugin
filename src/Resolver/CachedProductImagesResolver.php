<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Resolver;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CachedProductImagesResolver implements ProductImagesResolverInterface
{
    /** @var array<string, list<string>> */
    private array $images = [];

    public function __construct(private readonly ProductImagesResolverInterface $decorated)
    {
    }

    public function resolve(ProductInterface|ProductVariantInterface $product): array
    {
        $hash = spl_object_hash($product);

        if (!array_key_exists($hash, $this->images)) {
            $this->images[$hash] = $this->decorated->resolve($product);
        }

        return $this->images[$hash];
    }
}
