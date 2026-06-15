<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapper;
use Setono\SyliusSEOPlugin\DependencyInjection\SetonoSyliusSEOExtension;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGenerator;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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

    #[Test]
    public function after_loading_the_correct_parameter_has_been_set(): void
    {
        $this->load();

        $this->assertContainerBuilderHasAlias(ProductVariantUrlGeneratorInterface::class, ProductVariantUrlGenerator::class);
    }

    #[Test]
    public function it_prepends_twig_hooks_for_the_seo_output(): void
    {
        $container = new ContainerBuilder();

        (new SetonoSyliusSEOExtension())->prepend($container);

        $config = $container->getExtensionConfig('sylius_twig_hooks');

        self::assertCount(1, $config);

        /** @var array{ hooks: array<string, mixed> } $hooksConfig */
        $hooksConfig = $config[0];

        self::assertArrayHasKey('sylius_shop.base#metatags', $hooksConfig['hooks']);
        self::assertArrayHasKey('sylius_admin.channel.create.content.form.sections', $hooksConfig['hooks']);
        self::assertArrayHasKey('sylius_admin.channel.update.content.form.sections', $hooksConfig['hooks']);
    }

    #[Test]
    public function it_registers_the_website_structured_data_when_enabled(): void
    {
        $this->load([
            'structured_data' => [
                'website' => [
                    'enabled' => true,
                    'search_url_template' => [
                        'route' => 'sylius_shop_homepage',
                        'query_parameter' => 'q',
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasService(WebsiteDataMapper::class);
        $this->assertContainerBuilderHasParameter('setono_sylius_seo.structured_data.website.search_url_template');
    }
}
