<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface as CoreChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Builds absolute URLs for a page's channel from the command line, where there is no incoming
 * request to derive the host/scheme from. The path comes from the router; the authority comes
 * from the configured base URL, or the channel hostname plus the configured scheme.
 */
final class ChannelUrlGenerator
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly string $scheme = 'https',
        private readonly ?string $baseUrl = null,
    ) {
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

        $path = $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);

        return $this->authority($page->getChannel()) . $path;
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

    private function authority(?ChannelInterface $channel): string
    {
        if (null !== $this->baseUrl && '' !== $this->baseUrl) {
            return rtrim($this->baseUrl, '/');
        }

        $hostname = $channel?->getHostname();
        if (null === $hostname || '' === $hostname) {
            $hostname = 'localhost';
        }

        return $this->scheme . '://' . $hostname;
    }
}
