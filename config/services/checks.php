<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusSEOPlugin\Checker\CheckRunner;
use Setono\SyliusSEOPlugin\Checker\CheckRunnerInterface;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\CanonicalPresentDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\ElementContentDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\ElementExistsDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\H1Detector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\HeaderDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\HtmlLangDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\HttpStatusDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\ImageAltDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\JsonLdValidDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\MetaDescriptionLengthDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\MetaDescriptionPresentDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\MetaRobotsNoindexDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\MixedContentDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\OpenGraphDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\StatusCodeDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\TitleLengthDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\TitlePresentDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\ViewportMetaDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\XRobotsTagNoindexDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\DetectorRegistry;
use Setono\SyliusSEOPlugin\Checker\Detector\DetectorRegistryInterface;
use Setono\SyliusSEOPlugin\Checker\Fetcher\HttpClientPageFetcher;
use Setono\SyliusSEOPlugin\Checker\Fetcher\PageFetcherInterface;
use Setono\SyliusSEOPlugin\Checker\IssuePersister;
use Setono\SyliusSEOPlugin\Checker\IssuePersisterInterface;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\ChannelUrlGenerator;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\CompositeUrlResolver;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\CustomRoutePageUrlResolver;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\HomepagePageUrlResolver;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\ProductPageUrlResolver;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\TaxonPageUrlResolver;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\UrlResolverInterface;
use Setono\SyliusSEOPlugin\Command\DetectIssuesCommand;
use Setono\SyliusSEOPlugin\Controller\Admin\IgnoreIssueAction;
use Setono\SyliusSEOPlugin\Controller\Admin\RunAllChecksAction;
use Setono\SyliusSEOPlugin\Controller\Admin\RunPageChecksAction;
use Setono\SyliusSEOPlugin\Controller\Admin\UnignoreIssueAction;
use Setono\SyliusSEOPlugin\EventSubscriber\AdminMenuSubscriber;
use Setono\SyliusSEOPlugin\Form\Type\Check\ElementContentConfigType;
use Setono\SyliusSEOPlugin\Form\Type\Check\ElementExistsConfigType;
use Setono\SyliusSEOPlugin\Form\Type\Check\HeaderConfigType;
use Setono\SyliusSEOPlugin\Form\Type\Check\StatusCodeConfigType;
use Setono\SyliusSEOPlugin\Form\Type\CheckAssignmentType;
use Setono\SyliusSEOPlugin\Form\Type\PageType;
use Setono\SyliusSEOPlugin\Model\Page;
use Setono\SyliusSEOPlugin\Repository\IssueRepositoryInterface;
use Setono\SyliusSEOPlugin\Repository\PageRepositoryInterface;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent;

/*
 * Services for the SEO "checks" feature (issue #2): user-defined pages are fetched over HTTP
 * and run through a battery of generic issue detectors, producing issues shown in the admin.
 */
return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    /*
     * Detectors (the check battery). Tag a service with `setono_sylius_seo.issue_detector` (or
     * implement IssueDetectorInterface and rely on autoconfiguration) to add your own check.
     */
    $detectors = [
        HttpStatusDetector::class,
        StatusCodeDetector::class,
        XRobotsTagNoindexDetector::class,
        MetaRobotsNoindexDetector::class,
        TitlePresentDetector::class,
        TitleLengthDetector::class,
        MetaDescriptionPresentDetector::class,
        MetaDescriptionLengthDetector::class,
        H1Detector::class,
        CanonicalPresentDetector::class,
        ImageAltDetector::class,
        HtmlLangDetector::class,
        ViewportMetaDetector::class,
        OpenGraphDetector::class,
        JsonLdValidDetector::class,
        MixedContentDetector::class,
        ElementExistsDetector::class,
        ElementContentDetector::class,
        HeaderDetector::class,
    ];
    foreach ($detectors as $detector) {
        $services->set($detector)->tag('setono_sylius_seo.issue_detector');
    }

    // Configuration form types for the parametric detectors
    $services->set(ElementExistsConfigType::class)->tag('form.type');
    $services->set(ElementContentConfigType::class)->tag('form.type');
    $services->set(HeaderConfigType::class)->tag('form.type');
    $services->set(StatusCodeConfigType::class)->tag('form.type');

    // The registry indexes every tagged detector by its code, for the runner and the admin form.
    $services->set(DetectorRegistry::class)
        ->args([tagged_iterator('setono_sylius_seo.issue_detector')])
    ;
    $services->alias(DetectorRegistryInterface::class, DetectorRegistry::class);

    // Repositories — the resource repositories registered by the extension's registerResources() call
    $services->alias(PageRepositoryInterface::class, 'setono_sylius_seo.repository.page');
    $services->alias(IssueRepositoryInterface::class, 'setono_sylius_seo.repository.issue');

    // URL resolution: builds the absolute URL to fetch for each page
    $services->set(ChannelUrlGenerator::class)
        ->args([service('router')])
    ;

    $services->set(HomepagePageUrlResolver::class)
        ->args([service(ChannelUrlGenerator::class)])
        ->tag('setono_sylius_seo.page_url_resolver', ['priority' => -100])
    ;
    $services->set(ProductPageUrlResolver::class)
        ->args([service(ChannelUrlGenerator::class), service('sylius.repository.product')])
        ->tag('setono_sylius_seo.page_url_resolver', ['priority' => -110])
    ;
    $services->set(TaxonPageUrlResolver::class)
        ->args([service(ChannelUrlGenerator::class), service('sylius.repository.taxon')])
        ->tag('setono_sylius_seo.page_url_resolver', ['priority' => -120])
    ;
    $services->set(CustomRoutePageUrlResolver::class)
        ->args([service(ChannelUrlGenerator::class)])
        ->tag('setono_sylius_seo.page_url_resolver', ['priority' => -130])
    ;

    $services->set(CompositeUrlResolver::class)
        ->args([tagged_iterator('setono_sylius_seo.page_url_resolver')])
    ;
    $services->alias(UrlResolverInterface::class, CompositeUrlResolver::class);

    // Fetching
    $services->set(HttpClientPageFetcher::class)
        ->args([service('http_client')])
    ;
    $services->alias(PageFetcherInterface::class, HttpClientPageFetcher::class);

    // Persisting detected issues by fingerprint
    $services->set(IssuePersister::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service(IssueRepositoryInterface::class),
            service('clock'),
        ])
    ;
    $services->alias(IssuePersisterInterface::class, IssuePersister::class);

    // The runner ties it all together
    $services->set(CheckRunner::class)
        ->args([
            service(UrlResolverInterface::class),
            service(PageFetcherInterface::class),
            service(DetectorRegistryInterface::class),
            service(IssuePersisterInterface::class),
            service(PageRepositoryInterface::class),
            service('logger'),
        ])
    ;
    $services->alias(CheckRunnerInterface::class, CheckRunner::class);

    // Console command
    $services->set(DetectIssuesCommand::class)
        ->args([
            service(CheckRunnerInterface::class),
            service(PageRepositoryInterface::class),
            service('sylius.repository.channel'),
        ])
        ->tag('console.command')
    ;

    // Admin form types
    $services->set(PageType::class)
        ->args([service(CompositeUrlResolver::class), service('sylius.provider.locale')])
        ->tag('form.type')
    ;
    $services->set(CheckAssignmentType::class)
        ->args([service(DetectorRegistryInterface::class)])
        ->tag('form.type')
    ;

    // Render the page form as a Sylius live component so the dynamic "checks" collection (add/remove
    // rows) works. The matching twig hook is registered in the extension's prepend().
    $services->set('setono_sylius_seo.twig.component.page.form', ResourceFormComponent::class)
        ->args([
            service('setono_sylius_seo.repository.page'),
            service('form.factory'),
            Page::class,
            PageType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:setono_sylius_seo_page:form'])
    ;

    // Admin menu
    $services->set(AdminMenuSubscriber::class)
        ->tag('kernel.event_subscriber')
    ;

    // Admin controllers (custom actions)
    $services->set(RunPageChecksAction::class)
        ->args([
            service('router'),
            service('translator'),
            service(PageRepositoryInterface::class),
            service(CheckRunnerInterface::class),
        ])
        ->tag('controller.service_arguments')
    ;
    $services->set(RunAllChecksAction::class)
        ->args([
            service('router'),
            service('translator'),
            service(CheckRunnerInterface::class),
        ])
        ->tag('controller.service_arguments')
    ;
    $services->set(IgnoreIssueAction::class)
        ->args([
            service('router'),
            service('translator'),
            service(IssueRepositoryInterface::class),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('controller.service_arguments')
    ;
    $services->set(UnignoreIssueAction::class)
        ->args([
            service('router'),
            service('translator'),
            service(IssueRepositoryInterface::class),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('controller.service_arguments')
    ;
};
