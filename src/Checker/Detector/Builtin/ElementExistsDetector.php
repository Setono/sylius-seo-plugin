<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\ConfigurableIssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Form\Type\Check\ElementExistsConfigType;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * A backend-configurable check: asserts that the number of elements matching a CSS or XPath
 * selector is within the configured range (defaults to "at least one").
 */
final class ElementExistsDetector extends AbstractDetector implements ConfigurableIssueDetectorInterface
{
    public function getCode(): string
    {
        return 'element_exists';
    }

    public function getConfigFormType(): string
    {
        return ElementExistsConfigType::class;
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
        $min = $this->configInt($config, 'min') ?? 1;
        $max = $this->configInt($config, 'max');

        $count = 'xpath' === $selectorType ? $crawler->filterXPath($selector)->count() : $crawler->filter($selector)->count();

        if ($count < $min || (null !== $max && $count > $max)) {
            yield new DetectedIssue(
                $this->getCode(),
                $this->severityFromConfig($config, Severity::Warning),
                'setono_sylius_seo.issue.element_exists',
                ['%selector%' => $selector, '%count%' => $count, '%min%' => $min, '%max%' => $max ?? '∞'],
                ['selector' => $selector, 'selectorType' => $selectorType, 'count' => $count, 'min' => $min, 'max' => $max],
            );
        }
    }
}
