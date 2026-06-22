<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\Twig\JsonLdExtension;
use Setono\SyliusSEOPlugin\Twig\OpenGraphExtension;
use Setono\SyliusSEOPlugin\Twig\RobotsTxtExtension;
use Spatie\SchemaOrg\Graph;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(JsonLdExtension::class)
        ->args([
            service(Graph::class),
        ])
        ->tag('twig.extension')
    ;

    $services->set(RobotsTxtExtension::class)
        ->args([
            param('sylius_core.public_dir'),
        ])
        ->tag('twig.extension')
    ;

    $services->set(OpenGraphExtension::class)
        ->args([
            service(OpenGraph::class),
        ])
        ->tag('twig.extension')
    ;
};
