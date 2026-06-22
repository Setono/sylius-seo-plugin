<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Checker;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Checker\CheckRunner;
use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\DetectorRegistryInterface;
use Setono\SyliusSEOPlugin\Checker\Detector\IssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\Fetcher\PageFetcherInterface;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Checker\IssuePersisterInterface;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\UrlResolverInterface;
use Setono\SyliusSEOPlugin\Model\Page;
use Setono\SyliusSEOPlugin\Model\Severity;
use Setono\SyliusSEOPlugin\Repository\PageRepositoryInterface;

final class CheckRunnerTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_runs_only_the_selected_checks_and_persists_their_issues(): void
    {
        $page = new Page();
        $page->setChecks([['id' => '1', 'code' => 'a', 'config' => []]]);

        $detectedIssue = new DetectedIssue('a', Severity::Warning, 'message');

        $detector = $this->prophesize(IssueDetectorInterface::class);
        $detector->detect(Argument::type(Inspection::class), [])->willReturn([$detectedIssue]);

        $registry = $this->prophesize(DetectorRegistryInterface::class);
        $registry->get('a')->willReturn($detector->reveal());

        $persister = $this->prophesize(IssuePersisterInterface::class);
        $persister->upsert($page, 'https://example.com/', Argument::type('array'), $detectedIssue)->willReturn('fp')->shouldBeCalledOnce();
        $persister->resolveMissing($page, ['fp'])->shouldBeCalledOnce();

        $this->runner($registry, $persister)->run($page);
    }

    #[Test]
    public function it_skips_unknown_check_codes(): void
    {
        $page = new Page();
        $page->setChecks([['id' => '1', 'code' => 'unknown', 'config' => []]]);

        $registry = $this->prophesize(DetectorRegistryInterface::class);
        $registry->get('unknown')->willReturn(null);

        $persister = $this->prophesize(IssuePersisterInterface::class);
        $persister->upsert(Argument::cetera())->shouldNotBeCalled();
        $persister->resolveMissing($page, [])->shouldBeCalledOnce();

        $this->runner($registry, $persister)->run($page);
    }

    #[Test]
    public function it_swallows_a_throwing_detector_and_still_resolves_missing(): void
    {
        $page = new Page();
        $page->setChecks([['id' => '1', 'code' => 'a', 'config' => []]]);

        $detector = $this->prophesize(IssueDetectorInterface::class);
        $detector->detect(Argument::cetera())->willThrow(new \RuntimeException('boom'));

        $registry = $this->prophesize(DetectorRegistryInterface::class);
        $registry->get('a')->willReturn($detector->reveal());

        $persister = $this->prophesize(IssuePersisterInterface::class);
        $persister->resolveMissing($page, [])->shouldBeCalledOnce();

        $this->runner($registry, $persister)->run($page);
    }

    /**
     * @param \Prophecy\Prophecy\ObjectProphecy<DetectorRegistryInterface> $registry
     * @param \Prophecy\Prophecy\ObjectProphecy<IssuePersisterInterface> $persister
     */
    private function runner(object $registry, object $persister): CheckRunner
    {
        $urlResolver = $this->prophesize(UrlResolverInterface::class);
        $urlResolver->resolve(Argument::type(Page::class))->willReturn('https://example.com/');

        $fetcher = $this->prophesize(PageFetcherInterface::class);
        $fetcher->fetch(Argument::type(Page::class), 'https://example.com/')->willReturn(
            new Inspection(new Page(), 'https://example.com/', 200, [], '<html></html>', 'text/html'),
        );

        return new CheckRunner(
            $urlResolver->reveal(),
            $fetcher->reveal(),
            $registry->reveal(),
            $persister->reveal(),
            $this->prophesize(PageRepositoryInterface::class)->reveal(),
        );
    }
}
