<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusSEOPlugin\Checker\Detector\DetectorRegistryInterface;
use Setono\SyliusSEOPlugin\Checker\Fetcher\PageFetcherInterface;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\UrlResolverInterface;
use Setono\SyliusSEOPlugin\Model\PageInterface;
use Setono\SyliusSEOPlugin\Repository\PageRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

final class CheckRunner implements CheckRunnerInterface
{
    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly UrlResolverInterface $urlResolver,
        private readonly PageFetcherInterface $pageFetcher,
        private readonly DetectorRegistryInterface $detectorRegistry,
        private readonly IssuePersisterInterface $issuePersister,
        private readonly PageRepositoryInterface $pageRepository,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function run(PageInterface $page): void
    {
        $url = $this->urlResolver->resolve($page);
        $inspection = $this->pageFetcher->fetch($page, $url);

        $seenFingerprints = [];

        foreach ($page->getChecks() as $assignment) {
            $detector = $this->detectorRegistry->get($assignment['code']);
            if (null === $detector) {
                $this->logger->warning('Unknown check "{code}" assigned to page {page}', [
                    'code' => $assignment['code'],
                    'page' => $page->getId(),
                ]);

                continue;
            }

            try {
                foreach ($detector->detect($inspection, $assignment['config']) as $detectedIssue) {
                    $seenFingerprints[] = $this->issuePersister->upsert($page, $url, $assignment, $detectedIssue);
                }
            } catch (\Throwable $e) {
                $this->logger->error('Check "{code}" failed on page {page}: {message}', [
                    'code' => $assignment['code'],
                    'page' => $page->getId(),
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]);
            }
        }

        $this->issuePersister->resolveMissing($page, $seenFingerprints);
    }

    public function runAll(?ChannelInterface $channel = null): void
    {
        foreach ($this->pageRepository->findEnabled($channel) as $page) {
            try {
                $this->run($page);
            } catch (\Throwable $e) {
                $this->logger->error('Failed to run checks for page {page}: {message}', [
                    'page' => $page->getId(),
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]);
            }
        }
    }
}
