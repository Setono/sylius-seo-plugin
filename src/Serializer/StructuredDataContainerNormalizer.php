<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Serializer;

use Setono\SyliusSEOPlugin\StructuredData\StructuredDataContainer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class StructuredDataContainerNormalizer implements NormalizerInterface
{
    public function __construct(private readonly NormalizerInterface $normalizer)
    {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        return [
            '@context' => 'https://schema.org',
            '@graph' => $data,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof StructuredDataContainer;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            StructuredDataContainer::class => true,
        ];
    }
}
