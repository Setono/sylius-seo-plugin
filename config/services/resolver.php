<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\Resolver\CachedProductImagesResolver;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolver;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ProductImagesResolver::class)
        ->args([
            service('sylius.resolver.product_variant.default'),
            service('liip_imagine.cache.manager'),
        ])
    ;

    $services->alias(ProductImagesResolverInterface::class, ProductImagesResolver::class);

    $services->set(CachedProductImagesResolver::class)
        ->decorate(ProductImagesResolverInterface::class, null, 64)
        ->args([
            service('.inner'),
        ])
    ;
};
