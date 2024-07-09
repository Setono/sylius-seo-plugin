<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DependencyInjection;

use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGenerator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_seo');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        /** @psalm-suppress MixedMethodCall,UndefinedInterfaceMethod,PossiblyNullReference */
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('product_variant_url_generator')
                    ->defaultValue(ProductVariantUrlGenerator::class)
                    ->info('This is the service id of the product variant url generator. You can change this to your own implementation.')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('structured_data')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product')
                            ->canBeDisabled()
                        ->end()
                        ->arrayNode('online_store')
                            ->canBeDisabled()
                        ->end()
                        ->arrayNode('website')
                            ->canBeEnabled()
                            ->children()
                            ->arrayNode('search_url_template')
                                ->children()
                                    ->scalarNode('route')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('query_parameter')
                                        ->isRequired()
                                        ->cannotBeEmpty()
        ;

        return $treeBuilder;
    }
}
