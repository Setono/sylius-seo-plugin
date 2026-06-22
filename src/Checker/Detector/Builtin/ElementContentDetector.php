<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Flow\JSONPath\JSONPath;
use Flow\JSONPath\JSONPathException;
use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\ConfigurableIssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Form\Type\Check\ElementContentConfigType;
use Setono\SyliusSEOPlugin\Model\Severity;
use Symfony\Component\DomCrawler\Crawler;

/**
 * A backend-configurable check: asserts something about the content of an element selected by a
 * CSS or XPath selector, optionally reading an attribute and/or first extracting a value via JSON
 * path (for JSON-bearing tags such as JSON-LD). Lets operators define ad-hoc tests with no code.
 */
final class ElementContentDetector extends AbstractDetector implements ConfigurableIssueDetectorInterface
{
    public function getCode(): string
    {
        return 'element_content';
    }

    public function getConfigFormType(): string
    {
        return ElementContentConfigType::class;
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        if (!$inspection->isHtml()) {
            return;
        }

        $crawler = $inspection->getCrawler();
        if (null === $crawler) {
            return;
        }

        $selector = $this->configString($config, 'selector');
        if (null === $selector) {
            return;
        }

        $selectorType = $this->configString($config, 'selectorType') ?? 'css';
        $assertion = $this->configString($config, 'assertion') ?? Assertion::EXISTS;
        $attribute = $this->configString($config, 'attribute');
        $jsonPath = $this->configString($config, 'jsonPath');
        $expected = $this->configString($config, 'value');

        $actual = $this->actual($crawler, $selector, $selectorType, $attribute, $jsonPath);

        if (Assertion::satisfied($assertion, $actual, $expected)) {
            return;
        }

        yield new DetectedIssue(
            $this->getCode(),
            $this->severityFromConfig($config, Severity::Warning),
            'setono_sylius_seo.issue.element_content',
            [
                '%selector%' => $selector,
                '%assertion%' => $assertion,
                '%value%' => $expected ?? '',
                '%actual%' => $actual ?? '',
            ],
            [
                'selector' => $selector,
                'selectorType' => $selectorType,
                'assertion' => $assertion,
                'attribute' => $attribute,
                'jsonPath' => $jsonPath,
                'expected' => $expected,
                'actual' => $actual,
            ],
        );
    }

    private function actual(Crawler $crawler, string $selector, string $selectorType, ?string $attribute, ?string $jsonPath): ?string
    {
        $node = 'xpath' === $selectorType ? $crawler->filterXPath($selector) : $crawler->filter($selector);
        if (0 === $node->count()) {
            return null;
        }

        $first = $node->first();
        $raw = null !== $attribute ? $first->attr($attribute) : trim($first->text(''));
        if (null === $raw) {
            return null;
        }

        if (null !== $jsonPath) {
            return self::extractJsonPath($raw, $jsonPath);
        }

        return $raw;
    }

    private static function extractJsonPath(string $json, string $jsonPath): ?string
    {
        $data = json_decode($json, true);
        if (!\is_array($data)) {
            return null;
        }

        try {
            $result = (new JSONPath($data))->find($jsonPath);
        } catch (JSONPathException) {
            return null;
        }

        if (0 === \count($result)) {
            return null;
        }

        $value = $result[0];
        if (\is_scalar($value)) {
            return (string) $value;
        }

        $encoded = json_encode($value);

        return false !== $encoded ? $encoded : null;
    }
}
