<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Checker\Detector\Builtin;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\HttpStatusDetector;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Page;
use Setono\SyliusSEOPlugin\Model\Severity;

final class HttpStatusDetectorTest extends TestCase
{
    #[Test]
    public function it_is_silent_on_200(): void
    {
        self::assertCount(0, $this->detect(200));
    }

    #[Test]
    public function it_reports_an_error_on_404(): void
    {
        $issues = $this->detect(404);

        self::assertCount(1, $issues);
        self::assertSame(Severity::Error, $issues[0]->severity);
    }

    #[Test]
    public function it_reports_a_critical_issue_on_500(): void
    {
        self::assertSame(Severity::Critical, $this->detect(500)[0]->severity);
    }

    #[Test]
    public function it_reports_a_critical_issue_when_the_page_is_unreachable(): void
    {
        self::assertSame(Severity::Critical, $this->detect(0)[0]->severity);
    }

    /**
     * @return list<\Setono\SyliusSEOPlugin\Checker\DetectedIssue>
     */
    private function detect(int $statusCode): array
    {
        $inspection = new Inspection(new Page(), 'https://example.com/', $statusCode, [], null, 'text/html');

        return array_values(iterator_to_array((new HttpStatusDetector())->detect($inspection)));
    }
}
