<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use Setono\SyliusSEOPlugin\Twig\JsonLdExtension;
use Spatie\SchemaOrg\Graph;
use Twig\Extension\ExtensionInterface;
use Twig\Test\IntegrationTestCase;

final class JsonLdIntegrationTest extends IntegrationTestCase
{
    private Graph $graph;

    protected function setUp(): void
    {
        parent::setUp();

        $this->graph = new Graph();
    }

    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/Fixtures/json_ld';
    }

    // Twig < 3.13 (resolved when sylius/sylius is removed during static analysis) still has
    // getFixturesDir() as an abstract method; implement it so the class stays concrete there.
    protected function getFixturesDir(): string
    {
        return self::getFixturesDirectory();
    }

    /**
     * @return list<ExtensionInterface>
     */
    protected function getExtensions(): array
    {
        return [new JsonLdExtension($this->graph)];
    }
}
