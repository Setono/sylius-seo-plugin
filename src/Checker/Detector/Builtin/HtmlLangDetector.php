<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector\Builtin;

use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\AbstractDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

/**
 * Flags a document without a `lang` attribute on `<html>`.
 */
final class HtmlLangDetector extends AbstractDetector
{
    public function getCode(): string
    {
        return 'html_lang';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $this->htmlCrawler($inspection);
        if (null === $crawler) {
            return;
        }

        $lang = $this->firstAttribute($crawler, 'html', 'lang');
        if (null === $lang || '' === trim($lang)) {
            yield new DetectedIssue(
                $this->getCode(),
                Severity::Notice,
                'setono_sylius_seo.issue.html_lang',
            );
        }
    }
}
