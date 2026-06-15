<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\Twig\OpenGraphExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class OpenGraphExtensionTest extends TestCase
{
    #[Test]
    public function it_renders_empty_open_graph_with_only_type(): void
    {
        $output = $this->render('{{ setono_sylius_seo_render_open_graph() }}');

        self::assertSame('<meta property="og:type" content="website">', $output);
    }

    #[Test]
    public function it_renders_open_graph_with_title_set_via_global(): void
    {
        $output = $this->render(
            "{% do openGraph.title('My Page Title') %}\n{{ setono_sylius_seo_render_open_graph() }}",
        );

        self::assertSame(
            "<meta property=\"og:title\" content=\"My Page Title\">\n" .
            '<meta property="og:type" content="website">',
            $output,
        );
    }

    #[Test]
    public function it_uses_the_last_value_when_a_property_is_set_multiple_times(): void
    {
        $output = $this->render(
            "{% do openGraph.title('Title 1') %}\n{% do openGraph.title('Title 2') %}\n{{ setono_sylius_seo_render_open_graph() }}",
        );

        self::assertSame(
            "<meta property=\"og:title\" content=\"Title 2\">\n" .
            '<meta property="og:type" content="website">',
            $output,
        );
    }

    #[Test]
    public function it_escapes_html_entities_in_content(): void
    {
        $output = $this->render(
            "{% do openGraph.title('Title with \"quotes\" & <special> chars') %}\n{{ setono_sylius_seo_render_open_graph() }}",
        );

        self::assertSame(
            '<meta property="og:title" content="Title with &quot;quotes&quot; &amp; &lt;special&gt; chars">' . "\n" .
            '<meta property="og:type" content="website">',
            $output,
        );
    }

    #[Test]
    public function it_renders_open_graph_with_multiple_properties(): void
    {
        $output = $this->render(
            "{% do openGraph.title('My Page').description('Page description').url('https://example.com').siteName('Example Site') %}\n{{ setono_sylius_seo_render_open_graph() }}",
        );

        self::assertSame(
            "<meta property=\"og:title\" content=\"My Page\">\n" .
            "<meta property=\"og:type\" content=\"website\">\n" .
            "<meta property=\"og:url\" content=\"https://example.com\">\n" .
            "<meta property=\"og:description\" content=\"Page description\">\n" .
            '<meta property="og:site_name" content="Example Site">',
            $output,
        );
    }

    private function render(string $template): string
    {
        $twig = new Environment(new ArrayLoader(['template' => $template]), ['strict_variables' => true]);
        $twig->addExtension(new OpenGraphExtension(new OpenGraph()));

        return trim($twig->render('template'));
    }
}
