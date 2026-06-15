<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\Twig\JsonLdExtension;
use Spatie\SchemaOrg\Graph;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class JsonLdIntegrationTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_empty_json_ld_graph(): void
    {
        $output = $this->render(new Graph(), '{{ setono_sylius_seo_render_json_ld() }}');

        self::assertSame(
            '<script type="application/ld+json">{"@context":"https://schema.org","@graph":[]}</script>',
            $output,
        );
    }

    private function render(Graph $graph, string $template): string
    {
        $twig = new Environment(new ArrayLoader(['template' => $template]), ['strict_variables' => true]);
        $twig->addExtension(new JsonLdExtension($graph));

        return trim($twig->render('template'));
    }
}
