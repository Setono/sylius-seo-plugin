<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Serializer;

use Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\Offer\AggregateOffer;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Product;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductLinkedDataNormalizer implements NormalizerInterface
{
    public function __construct(private readonly NormalizerInterface $normalizer)
    {
    }

    /**
     * @param mixed|Product $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|null|\ArrayObject
    {
        if (!$this->supportsNormalization($object)) {
            throw new LogicException(sprintf('The object must be an instance of %s', Product::class));
        }

        if ($object->offers instanceof AggregateOffer && count($object->offers) === 1) {
            $object->offers = $object->offers[0];
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    /**
     * @psalm-assert-if-true Product $data
     */
    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof Product;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Product::class => true,
        ];
    }
}
