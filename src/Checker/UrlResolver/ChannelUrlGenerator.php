<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Component\Core\Model\ChannelInterface as CoreChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Builds absolute URLs for a page from the command line, where there is no incoming request.
 *
 * The scheme/host/port come from the router's request context (configure it with the framework's
 * `framework.router.default_uri` when running outside a request); the host is overridden with the
 * page's channel hostname so each channel is fetched on its own domain.
 */
final class ChannelUrlGenerator
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function generate(PageInterface $page, string $route, array $parameters = []): string
    {
        $localeCode = $this->localeCode($page);
        if (null !== $localeCode && !isset($parameters['_locale'])) {
            $parameters['_locale'] = $localeCode;
        }

        $context = $this->router->getContext();
        $originalHost = $context->getHost();

        $hostname = $page->getChannel()?->getHostname();

        try {
            if (null !== $hostname && '' !== $hostname) {
                $context->setHost($hostname);
            }

            return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        } finally {
            $context->setHost($originalHost);
        }
    }

    /**
     * The locale to resolve the page in: the page's own locale, else the channel's default locale.
     */
    public function localeCode(PageInterface $page): ?string
    {
        $localeCode = $page->getLocaleCode();

        $channel = $page->getChannel();
        if (null === $localeCode && $channel instanceof CoreChannelInterface) {
            $localeCode = $channel->getDefaultLocale()?->getCode();
        }

        return $localeCode;
    }
}
