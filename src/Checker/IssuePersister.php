<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Setono\SyliusSEOPlugin\Model\Issue;
use Setono\SyliusSEOPlugin\Model\IssueStatus;
use Setono\SyliusSEOPlugin\Model\PageInterface;
use Setono\SyliusSEOPlugin\Repository\IssueRepositoryInterface;

final class IssuePersister implements IssuePersisterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly IssueRepositoryInterface $issueRepository,
        private readonly ClockInterface $clock,
    ) {
    }

    public function upsert(PageInterface $page, string $url, array $assignment, DetectedIssue $detectedIssue): string
    {
        $fingerprint = self::fingerprint($page, $assignment, $detectedIssue);
        $now = $this->clock->now();

        $issue = $this->issueRepository->findOneByFingerprint($fingerprint);
        if (null === $issue) {
            $issue = new Issue();
            $issue->setFingerprint($fingerprint);
            $issue->setPage($page);
            $issue->setStatus(IssueStatus::Open);
            $issue->setFirstDetectedAt($now);
            $issue->setOccurrenceCount(1);

            $this->entityManager->persist($issue);
        } else {
            // a previously resolved issue that reappears becomes open again; an ignored issue
            // stays ignored (never resurrected) but we still record that it was seen
            if (IssueStatus::Resolved === $issue->getStatus()) {
                $issue->setStatus(IssueStatus::Open);
            }
            $issue->incrementOccurrenceCount();
        }

        // always refresh the presentational fields and the "last seen" timestamp
        $issue->setCheck($detectedIssue->check);
        $issue->setSeverity($detectedIssue->severity);
        $issue->setMessageTemplate($detectedIssue->messageTemplate);
        $issue->setMessageParameters($detectedIssue->messageParameters);
        $issue->setUrl($url);
        $issue->setContext($detectedIssue->context);
        $issue->setLastDetectedAt($now);

        $this->entityManager->flush();

        return $fingerprint;
    }

    public function resolveMissing(PageInterface $page, array $seenFingerprints): void
    {
        foreach ($this->issueRepository->findByPage($page) as $issue) {
            if (\in_array($issue->getFingerprint(), $seenFingerprints, true)) {
                continue;
            }

            if (IssueStatus::Open === $issue->getStatus()) {
                $issue->setStatus(IssueStatus::Resolved);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param array{id: string, code: string, config: array<string, mixed>} $assignment
     */
    private static function fingerprint(PageInterface $page, array $assignment, DetectedIssue $detectedIssue): string
    {
        return hash('xxh128', implode('|', [
            (string) $page->getId(),
            $assignment['id'],
            $detectedIssue->check,
            $detectedIssue->discriminator ?? '',
        ]));
    }
}
