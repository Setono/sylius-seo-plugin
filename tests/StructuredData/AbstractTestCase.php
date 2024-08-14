<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\StructuredData;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Context\Encoder\JsonEncoderContextBuilder;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Context\SerializerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractTestCase extends KernelTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        /** @psalm-suppress PropertyTypeCoercion */
        $this->serializer = $container->get('serializer');
    }

    /**
     * @test
     */
    public function it_serializes(): void
    {
        $this->assertSame($this->getExpectedJson(), $this->serializer->serialize(
            $this->getObject(),
            'json',
            (new SerializerContextBuilder())
            ->withContext((new ObjectNormalizerContextBuilder())->withSkipNullValues(true))
            ->withContext((new JsonEncoderContextBuilder())->withEncodeOptions(\JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_INVALID_UTF8_IGNORE | \JSON_PRESERVE_ZERO_FRACTION))
            ->toArray(),
        ));
    }

    abstract protected function getObject(): StructuredData;

    abstract protected function getExpectedJson(): string;

    protected static function getDate(string $date = '2020-01-01 00:00:00'): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);
        self::assertInstanceOf(\DateTimeImmutable::class, $date);

        return $date;
    }
}
