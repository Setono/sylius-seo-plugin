<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DependencyInjection;

use Setono\SyliusSEOPlugin\Checker\Detector\IssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\PageUrlResolverInterface;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapperInterface;
use Setono\SyliusSEOPlugin\Model\Issue;
use Setono\SyliusSEOPlugin\Model\IssueStatus;
use Setono\SyliusSEOPlugin\Model\Page;
use Setono\SyliusSEOPlugin\Model\Severity;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SetonoSyliusSEOExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{
         *     driver: string,
         *     resources: array<string, array<string, mixed>>,
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

        $this->registerResources('setono_sylius_seo', $config['driver'], $config['resources'], $container);

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

        $container
            ->registerForAutoconfiguration(IssueDetectorInterface::class)
            ->addTag('setono_sylius_seo.issue_detector')
        ;

        $container
            ->registerForAutoconfiguration(PageUrlResolverInterface::class)
            ->addTag('setono_sylius_seo.page_url_resolver')
        ;

        $container->setAlias(ProductVariantUrlGeneratorInterface::class, $config['product_variant_url_generator']);

        $loader->load('services.php');

        self::registerOnlineStoreConfig($config['structured_data']['online_store'], $container, $loader);
        self::registerProductConfig($config['structured_data']['product'], $container, $loader);
        self::registerWebsiteConfig($config['structured_data']['website'], $container, $loader);

        $loader->load('services/checks.php');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $robotsTxtSection = [
            'setono_sylius_seo_robots_txt' => [
                'template' => '@SetonoSyliusSEOPlugin/admin/channel/robots_txt.html.twig',
                'priority' => -10,
            ],
        ];

        // Render the "pages to test" create/update form inside a live component (see the component
        // registered in config/services/checks.php) so the dynamic "checks" collection works.
        $pageFormHookable = [
            'form' => [
                'component' => 'sylius_admin:setono_sylius_seo_page:form',
                'props' => [
                    'resource' => '@=_context.resource',
                    'form' => '@=_context.form',
                    'template' => '@SyliusAdmin/shared/crud/common/content/form.html.twig',
                ],
                'configuration' => [
                    'render_rest' => true,
                ],
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
                'sylius_admin.page.create.content' => $pageFormHookable,
                'sylius_admin.page.update.content' => $pageFormHookable,
            ],
        ]);

        $container->prependExtensionConfig('sylius_grid', self::gridConfiguration());
    }

    /**
     * @return array<string, mixed>
     */
    private static function gridConfiguration(): array
    {
        $severityChoices = [];
        foreach (Severity::cases() as $severity) {
            $severityChoices[ucfirst($severity->value)] = $severity->value;
        }

        $statusChoices = [];
        foreach (IssueStatus::cases() as $status) {
            $statusChoices[ucfirst($status->value)] = $status->value;
        }

        return [
            'grids' => [
                'setono_sylius_seo_admin_page' => [
                    'driver' => ['name' => 'doctrine/orm', 'options' => ['class' => Page::class]],
                    'sorting' => ['name' => 'asc'],
                    'fields' => [
                        'name' => ['type' => 'string', 'label' => 'sylius.ui.name', 'sortable' => null],
                        'channel' => ['type' => 'string', 'label' => 'sylius.ui.channel', 'path' => 'channel.name'],
                        'type' => ['type' => 'string', 'label' => 'sylius.ui.type'],
                        'checks' => ['type' => 'twig', 'label' => 'setono_sylius_seo.ui.checks_count', 'path' => '.', 'options' => ['template' => '@SetonoSyliusSEOPlugin/admin/page/grid/field/checks_count.html.twig']],
                        'enabled' => ['type' => 'twig', 'label' => 'sylius.ui.enabled', 'options' => ['template' => '@SyliusUi/grid/field/yes_no.html.twig']],
                    ],
                    'actions' => [
                        'main' => ['create' => ['type' => 'create']],
                        'item' => [
                            'run' => [
                                'type' => 'show',
                                'label' => 'setono_sylius_seo.ui.run_checks',
                                'icon' => 'refresh',
                                'options' => ['link' => ['route' => 'setono_sylius_seo_admin_page_run', 'parameters' => ['id' => 'resource.id']]],
                            ],
                            'update' => ['type' => 'update'],
                            'delete' => ['type' => 'delete'],
                        ],
                        'bulk' => ['delete' => ['type' => 'delete']],
                    ],
                ],
                'setono_sylius_seo_admin_issue' => [
                    'driver' => ['name' => 'doctrine/orm', 'options' => ['class' => Issue::class]],
                    'sorting' => ['lastDetectedAt' => 'desc'],
                    'fields' => [
                        'severity' => ['type' => 'twig', 'label' => 'setono_sylius_seo.ui.severity', 'path' => 'severity', 'options' => ['template' => '@SetonoSyliusSEOPlugin/admin/issue/grid/field/severity.html.twig']],
                        'check' => ['type' => 'twig', 'label' => 'setono_sylius_seo.ui.check', 'path' => '.', 'options' => ['template' => '@SetonoSyliusSEOPlugin/admin/issue/grid/field/check.html.twig']],
                        'message' => ['type' => 'twig', 'label' => 'setono_sylius_seo.ui.message', 'path' => '.', 'options' => ['template' => '@SetonoSyliusSEOPlugin/admin/issue/grid/field/message.html.twig']],
                        'url' => ['type' => 'string', 'label' => 'setono_sylius_seo.ui.url'],
                        'status' => ['type' => 'twig', 'label' => 'setono_sylius_seo.ui.status', 'path' => 'status', 'options' => ['template' => '@SetonoSyliusSEOPlugin/admin/issue/grid/field/status.html.twig']],
                        'lastDetectedAt' => ['type' => 'datetime', 'label' => 'setono_sylius_seo.ui.last_seen', 'options' => ['format' => 'Y-m-d H:i']],
                    ],
                    'filters' => [
                        'severity' => ['type' => 'select', 'options' => ['choices' => $severityChoices]],
                        'status' => ['type' => 'select', 'options' => ['choices' => $statusChoices]],
                        'check' => ['type' => 'string'],
                    ],
                    'actions' => [
                        'item' => [
                            'ignore' => [
                                'type' => 'show',
                                'label' => 'setono_sylius_seo.ui.ignore',
                                'icon' => 'eye-off',
                                'options' => ['link' => ['route' => 'setono_sylius_seo_admin_issue_ignore', 'parameters' => ['id' => 'resource.id']]],
                            ],
                            'unignore' => [
                                'type' => 'show',
                                'label' => 'setono_sylius_seo.ui.unignore',
                                'icon' => 'eye',
                                'options' => ['link' => ['route' => 'setono_sylius_seo_admin_issue_unignore', 'parameters' => ['id' => 'resource.id']]],
                            ],
                            'delete' => ['type' => 'delete'],
                        ],
                        'bulk' => ['delete' => ['type' => 'delete']],
                    ],
                ],
            ],
        ];
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
