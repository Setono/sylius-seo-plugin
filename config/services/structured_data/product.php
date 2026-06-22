<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\DataMapper\Product\CompositeProductDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Product\ImageProductDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Product\OffersProductDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\CompositeProductGroupDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\HasVariantProductGroupDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\EventSubscriber\StructuredData\AddItemListSubscriber;
use Setono\SyliusSEOPlugin\EventSubscriber\StructuredData\AddProductSubscriber;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Spatie\SchemaOrg\Graph;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Product Data Mappers
    $services->set(CompositeProductDataMapper::class)
        ->call('setLogger', [service('logger')])
    ;

    $services->alias(ProductDataMapperInterface::class, CompositeProductDataMapper::class);

    $services->set(ProductDataMapper::class)
        ->tag('setono_sylius_seo.product_data_mapper', ['priority' => -100])
    ;

    $services->set(OffersProductDataMapper::class)
        ->args([
            service('sylius.context.channel'),
            service(ProductVariantUrlGeneratorInterface::class),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('setono_sylius_seo.product_data_mapper', ['priority' => -110])
    ;

    $services->set(ImageProductDataMapper::class)
        ->args([
            service(ProductImagesResolverInterface::class),
        ])
        ->tag('setono_sylius_seo.product_data_mapper', ['priority' => -120])
    ;

    // Product Group Data Mappers
    $services->set(CompositeProductGroupDataMapper::class)
        ->call('setLogger', [service('logger')])
    ;

    $services->alias(ProductGroupDataMapperInterface::class, CompositeProductGroupDataMapper::class);

    $services->set(ProductGroupDataMapper::class)
        ->args([
            service('router'),
        ])
        ->tag('setono_sylius_seo.product_group_data_mapper', ['priority' => -100])
    ;

    $services->set(HasVariantProductGroupDataMapper::class)
        ->args([
            service(ProductDataMapperInterface::class),
        ])
        ->tag('setono_sylius_seo.product_group_data_mapper', ['priority' => -110])
    ;

    // Event subscriber on the product show page
    $services->set(AddProductSubscriber::class)
        ->args([
            service(Graph::class),
            service(ProductDataMapperInterface::class),
            service(ProductGroupDataMapperInterface::class),
            service('event_dispatcher'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    // Event subscriber on the product index / category page
    $services->set(AddItemListSubscriber::class)
        ->args([
            service(Graph::class),
            service(ProductDataMapperInterface::class),
            service('sylius.resolver.product_variant.default'),
        ])
        ->tag('kernel.event_subscriber')
    ;
};
