<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusSEOPlugin\DependencyInjection\SetonoSyliusSEOExtension;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGenerator;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyDependencyInjectionTest
 */
final class SetonoSyliusSEOExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusSEOExtension(),
        ];
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set(): void
    {
        $this->load();

        $this->assertContainerBuilderHasAlias(ProductVariantUrlGeneratorInterface::class, ProductVariantUrlGenerator::class);
    }
}
