<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Serializer;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Symfony\Component\Serializer\Context\Encoder\JsonEncoderContextBuilder;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Context\SerializerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

final class StructuredDataSerializer implements StructuredDataSerializerInterface
{
    public function __construct(private readonly SymfonySerializerInterface $serializer)
    {
    }

    public function serialize(StructuredData $structuredData): string
    {
        $context = (new SerializerContextBuilder())
            ->withContext((new ObjectNormalizerContextBuilder())->withSkipNullValues(true))
            ->withContext((new JsonEncoderContextBuilder())->withEncodeOptions(\JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_INVALID_UTF8_IGNORE | \JSON_PRESERVE_ZERO_FRACTION))
            ->toArray()
        ;

        return $this->serializer->serialize($structuredData, 'json', $context);
    }
}
