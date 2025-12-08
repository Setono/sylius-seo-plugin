<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Renderer;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\Renderer\RobotsTxtRenderer;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class RobotsTxtRendererTest extends TestCase
{
    /**
     * @test
     */
    public function it_renders_plain_robots_txt(): void
    {
        $twig = new Environment(new ArrayLoader());
        $renderer = new RobotsTxtRenderer($twig);

        $robotsTxt = <<<TXT
User-agent: *
Disallow: /admin
Allow: /
TXT;

        $result = $renderer->render($robotsTxt);

        self::assertSame($robotsTxt, $result);
    }

    /**
     * @test
     */
    public function it_renders_robots_txt_with_twig_expressions(): void
    {
        $twig = new Environment(new ArrayLoader());
        $twig->addGlobal('sitemap_url', 'https://example.com/sitemap.xml');

        $renderer = new RobotsTxtRenderer($twig);

        $robotsTxt = <<<TXT
User-agent: *
Disallow: /admin

Sitemap: {{ sitemap_url }}
TXT;

        $expected = <<<TXT
User-agent: *
Disallow: /admin

Sitemap: https://example.com/sitemap.xml
TXT;

        $result = $renderer->render($robotsTxt);

        self::assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function it_renders_empty_string(): void
    {
        $twig = new Environment(new ArrayLoader());
        $renderer = new RobotsTxtRenderer($twig);

        $result = $renderer->render('');

        self::assertSame('', $result);
    }

    /**
     * @test
     */
    public function it_renders_robots_txt_with_twig_conditionals(): void
    {
        $twig = new Environment(new ArrayLoader());
        $twig->addGlobal('is_production', true);

        $renderer = new RobotsTxtRenderer($twig);

        $robotsTxt = <<<TXT
User-agent: *
{% if is_production %}
Disallow: /admin
{% else %}
Disallow: /
{% endif %}
TXT;

        $result = $renderer->render($robotsTxt);

        self::assertStringContainsString('Disallow: /admin', $result);
        self::assertStringNotContainsString('Disallow: /', trim(str_replace('Disallow: /admin', '', $result)));
    }
}
