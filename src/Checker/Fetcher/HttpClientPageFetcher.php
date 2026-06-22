<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Fetcher;

use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\PageInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientPageFetcher implements PageFetcherInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $userAgent = 'SetonoSyliusSEOPlugin checker',
        private readonly int $maxBodySize = 5_000_000,
    ) {
    }

    public function fetch(PageInterface $page, string $url): Inspection
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'max_redirects' => 10,
                'headers' => ['User-Agent' => $this->userAgent],
            ]);

            $statusCode = $response->getStatusCode();
            $headers = $response->getHeaders(false);
            $contentType = $headers['content-type'][0] ?? 'text/html';

            $body = $response->getContent(false);
            if (\strlen($body) > $this->maxBodySize) {
                $body = substr($body, 0, $this->maxBodySize);
            }

            $finalUrl = $response->getInfo('url');
            $finalUrl = \is_string($finalUrl) ? $finalUrl : $url;

            return new Inspection($page, $finalUrl, $statusCode, $headers, $body, $contentType);
        } catch (TransportExceptionInterface) {
            return new Inspection($page, $url, 0, [], null, '');
        }
    }
}
