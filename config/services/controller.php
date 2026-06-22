<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\Controller\RenderRobotsTxtAction;
use Setono\SyliusSEOPlugin\Renderer\RobotsTxtRendererInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(RenderRobotsTxtAction::class)
        ->public()
        ->args([
            service('sylius.context.channel'),
            service(RobotsTxtRendererInterface::class),
        ])
    ;
};
