<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DependencyInjection;

use Setono\SyliusSEOPlugin\Form\Type\PageType;
use Setono\SyliusSEOPlugin\Model\Issue;
use Setono\SyliusSEOPlugin\Model\IssueInterface;
use Setono\SyliusSEOPlugin\Model\Page;
use Setono\SyliusSEOPlugin\Model\PageInterface;
use Setono\SyliusSEOPlugin\Repository\IssueRepository;
use Setono\SyliusSEOPlugin\Repository\PageRepository;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGenerator;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Resource\Factory\Factory;
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
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('driver')
                    ->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Page::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PageInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(PageRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(PageType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('issue')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Issue::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(IssueInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(IssueRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
