<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

use Sylius\Resource\Model\ResourceInterface;

/**
 * A persisted SEO issue detected on a page. Issues are upserted by their fingerprint so that
 * re-running detection updates existing rows (and an "ignored" decision survives across runs).
 */
interface IssueInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getFingerprint(): ?string;

    public function setFingerprint(?string $fingerprint): void;

    public function getPage(): ?PageInterface;

    public function setPage(?PageInterface $page): void;

    public function getCheck(): ?string;

    public function setCheck(?string $check): void;

    public function getSeverity(): ?Severity;

    public function setSeverity(?Severity $severity): void;

    public function getMessageTemplate(): ?string;

    public function setMessageTemplate(?string $messageTemplate): void;

    /**
     * @return array<string, scalar|null>
     */
    public function getMessageParameters(): array;

    /**
     * @param array<string, scalar|null> $messageParameters
     */
    public function setMessageParameters(array $messageParameters): void;

    public function getUrl(): ?string;

    public function setUrl(?string $url): void;

    public function getSubjectType(): ?string;

    public function setSubjectType(?string $subjectType): void;

    public function getSubjectId(): ?string;

    public function setSubjectId(?string $subjectId): void;

    public function getStatus(): IssueStatus;

    public function setStatus(IssueStatus $status): void;

    public function getFirstDetectedAt(): ?\DateTimeInterface;

    public function setFirstDetectedAt(?\DateTimeInterface $firstDetectedAt): void;

    public function getLastDetectedAt(): ?\DateTimeInterface;

    public function setLastDetectedAt(?\DateTimeInterface $lastDetectedAt): void;

    public function getOccurrenceCount(): int;

    public function setOccurrenceCount(int $occurrenceCount): void;

    public function incrementOccurrenceCount(): void;

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array;

    /**
     * @param array<string, mixed> $context
     */
    public function setContext(array $context): void;
}
