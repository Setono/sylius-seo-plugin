<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\CompositeOnlineStoreDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface;
use Setono\SyliusSEOPlugin\EventSubscriber\StructuredData\AddOnlineStoreSubscriber;
use Spatie\SchemaOrg\Graph;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Online Store Data Mappers
    $services->set(CompositeOnlineStoreDataMapper::class)
        ->call('setLogger', [service('logger')])
    ;

    $services->alias(OnlineStoreDataMapperInterface::class, CompositeOnlineStoreDataMapper::class);

    $services->set(OnlineStoreDataMapper::class)
        ->args([
            service('router'),
        ])
        ->tag('setono_sylius_seo.online_store_data_mapper', ['priority' => 100])
    ;

    // Event subscriber that will add the online store structured data to the homepage
    $services->set(AddOnlineStoreSubscriber::class)
        ->args([
            service(Graph::class),
            service(OnlineStoreDataMapperInterface::class),
            service('sylius.context.channel'),
            service('event_dispatcher'),
        ])
        ->tag('kernel.event_subscriber')
    ;
};
