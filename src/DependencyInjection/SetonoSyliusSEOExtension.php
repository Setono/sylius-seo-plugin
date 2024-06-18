<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DependencyInjection;

use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusSEOExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{product_variant_url_generator: string} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container
            ->registerForAutoconfiguration(ProductDataMapperInterface::class)
            ->addTag('setono_sylius_seo.product_data_mapper')
        ;

        $container
            ->registerForAutoconfiguration(ProductGroupDataMapperInterface::class)
            ->addTag('setono_sylius_seo.product_group_data_mapper')
        ;

        $container->setAlias(ProductVariantUrlGeneratorInterface::class, $config['product_variant_url_generator']);

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('sylius_ui', [
            'events' => [
                'sylius.shop.layout.head' => [
                    'blocks' => [
                        'setono_sylius_seo_json_ld' => [
                            'template' => '@SetonoSyliusSEOPlugin/json_ld.html.twig',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
