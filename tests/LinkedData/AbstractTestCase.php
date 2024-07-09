<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\LinkedData;

use Setono\SyliusSEOPlugin\Twig\JsonLdRuntime;
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
        $this->assertSame($this->getExpectedJson(), $this->serializer->serialize($this->getObject(), 'json', JsonLdRuntime::serializerContext()));
    }

    abstract protected function getObject(): object;

    abstract protected function getExpectedJson(): string;

    protected static function getDate(string $date = '2020-01-01 00:00:00'): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);
        self::assertInstanceOf(\DateTimeImmutable::class, $date);

        return $date;
    }
}
