<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusSEOPlugin\Serializer\StructuredDataSerializerInterface;
use Setono\SyliusSEOPlugin\StructuredData\StructuredDataContainerInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class JsonLdRuntime implements RuntimeExtensionInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly StructuredDataContainerInterface $structuredDataContainer,
        private readonly StructuredDataSerializerInterface $serializer,
    ) {
        $this->logger = new NullLogger();
    }

    public function renderJsonLd(): string
    {
        if ($this->structuredDataContainer->empty()) {
            return '';
        }

        $output = [];

        try {
            foreach ($this->structuredDataContainer as $linkedData) {
                foreach ($linkedData as $value) {
                    $output[] = $this->serializer->serialize($value);
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf('An error occurred while rendering JSON-LD: %s', $e->getMessage()),
                ['exception' => $e],
            );
        }

        return sprintf('<script type="application/ld+json">[%s]</script>', implode("\n", $output));
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
