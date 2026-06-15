<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\Renderer\RobotsTxtRenderer;
use Setono\SyliusSEOPlugin\Renderer\RobotsTxtRendererInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(RobotsTxtRenderer::class)
        ->args([
            service('twig'),
        ])
    ;

    $services->alias(RobotsTxtRendererInterface::class, RobotsTxtRenderer::class);
};
