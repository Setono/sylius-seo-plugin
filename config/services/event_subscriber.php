<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph\AddChannelInformationSubscriber;
use Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph\AddProductInformationSubscriber;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Open graph
    $services->set(AddChannelInformationSubscriber::class)
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service(OpenGraph::class),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set(AddProductInformationSubscriber::class)
        ->args([
            service(ProductImagesResolverInterface::class),
            service(OpenGraph::class),
        ])
        ->tag('kernel.event_subscriber')
    ;
};
