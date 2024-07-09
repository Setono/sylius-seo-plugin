<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin;

use Setono\CompositeCompilerPass\CompositeCompilerPass;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\CompositeOnlineStoreDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Product\CompositeProductDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\CompositeProductGroupDataMapper;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SetonoSyliusSEOPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeProductDataMapper::class,
            'setono_sylius_seo.product_data_mapper',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeProductGroupDataMapper::class,
            'setono_sylius_seo.product_group_data_mapper',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeOnlineStoreDataMapper::class,
            'setono_sylius_seo.online_store_data_mapper',
        ));
    }
}
