<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Checker\Detector\Builtin;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\ElementContentDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Page;

final class ElementContentDetectorTest extends TestCase
{
    private const HTML = '<html><head>'
        . '<script type="application/ld+json" id="seo-ggg">{"@type":"Product","offers":[{"price":"19.99"}]}</script>'
        . '</head><body><meta name="x" content="hello"></body></html>';

    #[Test]
    public function it_is_silent_when_the_contains_assertion_passes(): void
    {
        self::assertCount(0, $this->detect(['selector' => 'script#seo-ggg', 'assertion' => 'contains', 'value' => 'Product']));
    }

    #[Test]
    public function it_matches_script_tag_content_with_a_contains_assertion(): void
    {
        self::assertCount(1, $this->detect(['selector' => 'script#seo-ggg', 'assertion' => 'contains', 'value' => 'Missing']));
    }

    #[Test]
    public function it_extracts_a_value_with_json_path_and_compares_it(): void
    {
        $config = ['selector' => 'script#seo-ggg', 'jsonPath' => '$.offers[0].price', 'assertion' => 'equals'];

        self::assertCount(0, $this->detect($config + ['value' => '19.99']));
        self::assertCount(1, $this->detect($config + ['value' => '9.99']));
    }

    #[Test]
    public function it_supports_xpath_selectors(): void
    {
        self::assertCount(1, $this->detect([
            'selector' => '//link[@rel="canonical"]',
            'selectorType' => 'xpath',
            'assertion' => 'exists',
        ]));
    }

    #[Test]
    public function it_reads_an_attribute_when_configured(): void
    {
        self::assertCount(0, $this->detect([
            'selector' => 'meta[name="x"]',
            'attribute' => 'content',
            'assertion' => 'equals',
            'value' => 'hello',
        ]));
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return list<\Setono\SyliusSEOPlugin\Checker\DetectedIssue>
     */
    private function detect(array $config): array
    {
        $inspection = new Inspection(new Page(), 'https://example.com/', 200, [], self::HTML, 'text/html');

        return array_values(iterator_to_array((new ElementContentDetector())->detect($inspection, $config)));
    }
}
