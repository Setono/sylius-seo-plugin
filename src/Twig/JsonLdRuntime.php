<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusSEOPlugin\StructuredData\StructuredDataContainerInterface;
use Symfony\Component\Serializer\Context\Encoder\JsonEncoderContextBuilder;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Context\SerializerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class JsonLdRuntime implements RuntimeExtensionInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly StructuredDataContainerInterface $linkedDataContainer,
        private readonly SerializerInterface $serializer,
    ) {
        $this->logger = new NullLogger();
    }

    public function renderJsonLd(): string
    {
        if ($this->linkedDataContainer->empty()) {
            return '';
        }

        $output = [];

        try {
            $context = self::serializerContext();
            foreach ($this->linkedDataContainer as $linkedData) {
                foreach ($linkedData as $value) {
                    $output[] = $this->serializer->serialize($value, 'json', $context);
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

    /**
     * todo move serializer to a service
     *
     * @internal
     *
     * @return array<string, mixed>
     */
    public static function serializerContext(): array
    {
        return (new SerializerContextBuilder())
            ->withContext((new ObjectNormalizerContextBuilder())->withSkipNullValues(true))
            ->withContext((new JsonEncoderContextBuilder())->withEncodeOptions(\JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_INVALID_UTF8_IGNORE | \JSON_PRESERVE_ZERO_FRACTION))
            ->toArray()
        ;
    }
}
