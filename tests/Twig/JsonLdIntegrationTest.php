<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use Setono\SyliusSEOPlugin\Twig\JsonLdExtension;
use Spatie\SchemaOrg\Graph;
use Twig\Test\IntegrationTestCase;

final class JsonLdIntegrationTest extends IntegrationTestCase
{
    private Graph $graph;

    protected function setUp(): void
    {
        parent::setUp();

        $this->graph = new Graph();
    }

    public function getExtensions(): array
    {
        return [new JsonLdExtension($this->graph)];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/json_ld';
    }
}
