<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\StructuredData;

use Setono\SyliusSEOPlugin\Serializer\StructuredDataSerializer;
use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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
        $serializer = new StructuredDataSerializer($this->serializer);
        $this->assertSame($this->getExpectedJson(), $serializer->serialize($this->getObject()));
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
