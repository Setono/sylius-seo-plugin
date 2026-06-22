<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\Form\Extension\ChannelTypeExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ChannelTypeExtension::class)
        ->tag('form.type_extension')
    ;
};
