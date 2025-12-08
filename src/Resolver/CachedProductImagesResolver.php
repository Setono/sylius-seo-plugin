<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Resolver;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CachedProductImagesResolver implements ProductImagesResolverInterface
{
    /** @var list<string>|null */
    private ?array $images = null;

    public function __construct(private readonly ProductImagesResolverInterface $decorated)
    {
    }

    public function resolve(ProductInterface|ProductVariantInterface $product): array
    {
        if (null === $this->images) {
            $this->images = $this->decorated->resolve($product);
        }

        return $this->images;
    }
}
