<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Checker\Detector\Builtin;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\TitleLengthDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Page;

final class TitleLengthDetectorTest extends TestCase
{
    #[Test]
    public function it_reports_an_issue_when_the_title_is_too_short(): void
    {
        $issues = $this->detect('<html><head><title>Hi</title></head></html>');

        self::assertCount(1, $issues);
        self::assertSame('title_length', $issues[0]->check);
    }

    #[Test]
    public function it_reports_an_issue_when_the_title_is_too_long(): void
    {
        $issues = $this->detect('<html><head><title>' . str_repeat('a', 80) . '</title></head></html>');

        self::assertCount(1, $issues);
    }

    #[Test]
    public function it_is_silent_when_the_title_is_within_bounds(): void
    {
        self::assertCount(0, $this->detect('<html><head><title>A perfectly reasonable title</title></head></html>'));
    }

    #[Test]
    public function it_is_silent_when_there_is_no_title(): void
    {
        self::assertCount(0, $this->detect('<html><head></head></html>'));
    }

    #[Test]
    public function it_is_silent_for_a_non_successful_response(): void
    {
        $inspection = new Inspection(new Page(), 'https://example.com/', 404, [], '<html><head><title>Hi</title></head></html>', 'text/html');

        self::assertCount(0, iterator_to_array((new TitleLengthDetector())->detect($inspection)));
    }

    /**
     * @return list<\Setono\SyliusSEOPlugin\Checker\DetectedIssue>
     */
    private function detect(string $html): array
    {
        $inspection = new Inspection(new Page(), 'https://example.com/', 200, [], $html, 'text/html');

        return array_values(iterator_to_array((new TitleLengthDetector())->detect($inspection)));
    }
}
