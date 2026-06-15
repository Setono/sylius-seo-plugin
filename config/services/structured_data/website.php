<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\DataMapper\Website\CompositeWebsiteDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapperInterface;
use Setono\SyliusSEOPlugin\EventSubscriber\StructuredData\AddWebsiteSubscriber;
use Spatie\SchemaOrg\Graph;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // WebSite Data Mappers
    $services->set(CompositeWebsiteDataMapper::class)
        ->call('setLogger', [service('logger')])
    ;

    $services->alias(WebsiteDataMapperInterface::class, CompositeWebsiteDataMapper::class);

    $services->set(WebsiteDataMapper::class)
        ->args([
            service('router'),
            param('setono_sylius_seo.structured_data.website.search_url_template'),
        ])
        ->tag('setono_sylius_seo.website_data_mapper', ['priority' => 100])
    ;

    // Event subscriber that will add the website structured data to the homepage
    $services->set(AddWebsiteSubscriber::class)
        ->args([
            service(Graph::class),
            service(WebsiteDataMapperInterface::class),
            service('sylius.context.channel'),
            service('event_dispatcher'),
        ])
        ->tag('kernel.event_subscriber')
    ;
};
