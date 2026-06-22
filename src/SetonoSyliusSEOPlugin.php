<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Setono\CompositeCompilerPass\CompositeCompilerPass;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\CompositeOnlineStoreDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Product\CompositeProductDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\CompositeProductGroupDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Website\CompositeWebsiteDataMapper;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusSEOPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    /**
     * @return list<string>
     */
    public function getSupportedDrivers(): array
    {
        return [SyliusResourceBundle::DRIVER_DOCTRINE_ORM];
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container): void
    {
        // AbstractResourceBundle::build() only registers XML/YAML/annotation mappings, so we don't
        // call it. Our model uses PHP 8 attributes, registered with the attribute mapping driver.
        $container->addCompilerPass(DoctrineOrmMappingsPass::createAttributeMappingDriver(
            ['Setono\SyliusSEOPlugin\Model'],
            [$this->getPath() . '/src/Model'],
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeOnlineStoreDataMapper::class,
            'setono_sylius_seo.online_store_data_mapper',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeProductDataMapper::class,
            'setono_sylius_seo.product_data_mapper',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeProductGroupDataMapper::class,
            'setono_sylius_seo.product_group_data_mapper',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeWebsiteDataMapper::class,
            'setono_sylius_seo.website_data_mapper',
        ));
    }
}
