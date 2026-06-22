<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Checker;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Clock\ClockInterface;
use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\IssuePersister;
use Setono\SyliusSEOPlugin\Model\Issue;
use Setono\SyliusSEOPlugin\Model\IssueStatus;
use Setono\SyliusSEOPlugin\Model\Page;
use Setono\SyliusSEOPlugin\Model\Severity;
use Setono\SyliusSEOPlugin\Repository\IssueRepositoryInterface;

final class IssuePersisterTest extends TestCase
{
    use ProphecyTrait;

    private const ASSIGNMENT = ['id' => 'assignment-1', 'code' => 'title_length', 'config' => []];

    #[Test]
    public function it_inserts_a_new_open_issue(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $repository = $this->prophesize(IssueRepositoryInterface::class);
        $repository->findOneByFingerprint(Argument::type('string'))->willReturn(null);

        $entityManager->persist(Argument::type(Issue::class))->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $fingerprint = $this->persister($entityManager, $repository)->upsert(new Page(), 'https://example.com/', self::ASSIGNMENT, $this->detectedIssue());

        self::assertNotSame('', $fingerprint);
    }

    #[Test]
    public function it_bumps_an_existing_open_issue(): void
    {
        $issue = new Issue();
        $issue->setStatus(IssueStatus::Open);
        $issue->setOccurrenceCount(1);

        $this->upsertAgainst($issue);

        self::assertSame(2, $issue->getOccurrenceCount());
        self::assertSame(IssueStatus::Open, $issue->getStatus());
    }

    #[Test]
    public function it_reopens_a_resolved_issue_that_reappears(): void
    {
        $issue = new Issue();
        $issue->setStatus(IssueStatus::Resolved);

        $this->upsertAgainst($issue);

        self::assertSame(IssueStatus::Open, $issue->getStatus());
    }

    #[Test]
    public function it_never_resurrects_an_ignored_issue(): void
    {
        $issue = new Issue();
        $issue->setStatus(IssueStatus::Ignored);

        $this->upsertAgainst($issue);

        self::assertSame(IssueStatus::Ignored, $issue->getStatus());
    }

    #[Test]
    public function it_resolves_open_issues_that_were_not_seen_and_leaves_ignored_ones(): void
    {
        $open = $this->issueWithFingerprint('a', IssueStatus::Open);
        $seenOpen = $this->issueWithFingerprint('b', IssueStatus::Open);
        $ignored = $this->issueWithFingerprint('c', IssueStatus::Ignored);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->flush()->shouldBeCalled();
        $repository = $this->prophesize(IssueRepositoryInterface::class);
        $repository->findByPage(Argument::type(Page::class))->willReturn([$open, $seenOpen, $ignored]);

        $this->persister($entityManager, $repository)->resolveMissing(new Page(), ['b']);

        self::assertSame(IssueStatus::Resolved, $open->getStatus());
        self::assertSame(IssueStatus::Open, $seenOpen->getStatus());
        self::assertSame(IssueStatus::Ignored, $ignored->getStatus());
    }

    private function upsertAgainst(Issue $issue): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->flush()->shouldBeCalled();
        $repository = $this->prophesize(IssueRepositoryInterface::class);
        $repository->findOneByFingerprint(Argument::type('string'))->willReturn($issue);

        $this->persister($entityManager, $repository)->upsert(new Page(), 'https://example.com/', self::ASSIGNMENT, $this->detectedIssue());
    }

    private function issueWithFingerprint(string $fingerprint, IssueStatus $status): Issue
    {
        $issue = new Issue();
        $issue->setFingerprint($fingerprint);
        $issue->setStatus($status);

        return $issue;
    }

    private function detectedIssue(): DetectedIssue
    {
        return new DetectedIssue('title_length', Severity::Warning, 'setono_sylius_seo.issue.title_length');
    }

    /**
     * @param ObjectProphecy<EntityManagerInterface> $entityManager
     * @param ObjectProphecy<IssueRepositoryInterface> $repository
     */
    private function persister(ObjectProphecy $entityManager, ObjectProphecy $repository): IssuePersister
    {
        $clock = $this->prophesize(ClockInterface::class);
        $clock->now()->willReturn(new \DateTimeImmutable('2026-06-22 10:00:00'));

        return new IssuePersister($entityManager->reveal(), $repository->reveal(), $clock->reveal());
    }
}
