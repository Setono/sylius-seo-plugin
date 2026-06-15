<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DependencyInjection;

use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapperInterface;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SetonoSyliusSEOExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{
         *     product_variant_url_generator: string,
         *     structured_data: array{
         *         online_store: array{ enabled: bool },
         *         product: array{ enabled: bool },
         *         website: array{ enabled: bool, search_url_template?: array{ route: string, query_parameter: string } }
         *     }
         * } $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 2) . '/config'));

        $container
            ->registerForAutoconfiguration(OnlineStoreDataMapperInterface::class)
            ->addTag('setono_sylius_seo.online_store_data_mapper')
        ;

        $container
            ->registerForAutoconfiguration(ProductDataMapperInterface::class)
            ->addTag('setono_sylius_seo.product_data_mapper')
        ;

        $container
            ->registerForAutoconfiguration(ProductGroupDataMapperInterface::class)
            ->addTag('setono_sylius_seo.product_group_data_mapper')
        ;

        $container
            ->registerForAutoconfiguration(WebsiteDataMapperInterface::class)
            ->addTag('setono_sylius_seo.website_data_mapper')
        ;

        $container->setAlias(ProductVariantUrlGeneratorInterface::class, $config['product_variant_url_generator']);

        $loader->load('services.php');

        self::registerOnlineStoreConfig($config['structured_data']['online_store'], $container, $loader);
        self::registerProductConfig($config['structured_data']['product'], $container, $loader);
        self::registerWebsiteConfig($config['structured_data']['website'], $container, $loader);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $robotsTxtSection = [
            'setono_sylius_seo_robots_txt' => [
                'template' => '@SetonoSyliusSEOPlugin/admin/channel/robots_txt.html.twig',
                'priority' => -10,
            ],
        ];

        $container->prependExtensionConfig('sylius_twig_hooks', [
            'hooks' => [
                'sylius_shop.base#metatags' => [
                    'setono_sylius_seo_json_ld' => [
                        'template' => '@SetonoSyliusSEOPlugin/json_ld.html.twig',
                    ],
                    'setono_sylius_seo_open_graph' => [
                        'template' => '@SetonoSyliusSEOPlugin/open_graph.html.twig',
                    ],
                ],
                'sylius_admin.channel.create.content.form.sections' => $robotsTxtSection,
                'sylius_admin.channel.update.content.form.sections' => $robotsTxtSection,
            ],
        ]);
    }

    /**
     * @param array{ enabled: bool } $config
     */
    private static function registerOnlineStoreConfig(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('setono_sylius_seo.structured_data.online_store.enabled', $config['enabled']);

        if (!$config['enabled']) {
            return;
        }

        $loader->load('services/structured_data/online_store.php');
    }

    /**
     * @param array{ enabled: bool } $config
     */
    private static function registerProductConfig(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('setono_sylius_seo.structured_data.product.enabled', $config['enabled']);

        if (!$config['enabled']) {
            return;
        }

        $loader->load('services/structured_data/product.php');
    }

    /**
     * @param array{ enabled: bool, search_url_template?: array{ route: string, query_parameter: string } } $config
     */
    private static function registerWebsiteConfig(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('setono_sylius_seo.structured_data.website.enabled', $config['enabled']);

        if (!$config['enabled']) {
            return;
        }

        // todo this will give an error if the search_url_template isn't set
        $loader->load('services/structured_data/website.php');

        if (isset($config['search_url_template'])) {
            $container->setParameter('setono_sylius_seo.structured_data.website.search_url_template', $config['search_url_template']);
        }
    }
}
