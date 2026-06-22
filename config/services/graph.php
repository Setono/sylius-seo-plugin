<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Spatie\SchemaOrg\Graph;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(Graph::class);
};
