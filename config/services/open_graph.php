<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(OpenGraph::class);
};
