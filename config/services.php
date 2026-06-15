<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->import('services/controller.php');
    $container->import('services/event_subscriber.php');
    $container->import('services/form.php');
    $container->import('services/graph.php');
    $container->import('services/open_graph.php');
    $container->import('services/renderer.php');
    $container->import('services/resolver.php');
    $container->import('services/twig.php');
    $container->import('services/url_generator.php');
};
