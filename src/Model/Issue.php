<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusSEOPlugin\Repository\IssueRepository;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
#[ORM\Table(name: 'setono_sylius_seo__issue')]
#[ORM\UniqueConstraint(name: 'setono_sylius_seo__issue_fingerprint', columns: ['fingerprint'])]
class Issue implements IssueInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    protected ?string $fingerprint = null;

    #[ORM\ManyToOne(targetEntity: PageInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    protected ?PageInterface $page = null;

    // `check` is a reserved word; wrapping the column name in backticks tells Doctrine to quote it
    #[ORM\Column(name: '`check`', type: Types::STRING, nullable: true)]
    protected ?string $check = null;

    #[ORM\Column(type: Types::STRING, enumType: Severity::class, nullable: true)]
    protected ?Severity $severity = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $messageTemplate = null;

    /** @var array<string, scalar|null> */
    #[ORM\Column(type: Types::JSON)]
    protected array $messageParameters = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $url = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $subjectType = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $subjectId = null;

    #[ORM\Column(type: Types::STRING, enumType: IssueStatus::class, options: ['default' => IssueStatus::Open->value])]
    protected IssueStatus $status = IssueStatus::Open;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $firstDetectedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $lastDetectedAt = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 1])]
    protected int $occurrenceCount = 1;

    /** @var array<string, mixed> */
    #[ORM\Column(type: Types::JSON)]
    protected array $context = [];

    public function __toString(): string
    {
        return sprintf('%s: %s', (string) $this->check, (string) $this->messageTemplate);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }

    public function getPage(): ?PageInterface
    {
        return $this->page;
    }

    public function setPage(?PageInterface $page): void
    {
        $this->page = $page;
    }

    public function getCheck(): ?string
    {
        return $this->check;
    }

    public function setCheck(?string $check): void
    {
        $this->check = $check;
    }

    public function getSeverity(): ?Severity
    {
        return $this->severity;
    }

    public function setSeverity(?Severity $severity): void
    {
        $this->severity = $severity;
    }

    public function getMessageTemplate(): ?string
    {
        return $this->messageTemplate;
    }

    public function setMessageTemplate(?string $messageTemplate): void
    {
        $this->messageTemplate = $messageTemplate;
    }

    public function getMessageParameters(): array
    {
        return $this->messageParameters;
    }

    public function setMessageParameters(array $messageParameters): void
    {
        $this->messageParameters = $messageParameters;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function setSubjectType(?string $subjectType): void
    {
        $this->subjectType = $subjectType;
    }

    public function getSubjectId(): ?string
    {
        return $this->subjectId;
    }

    public function setSubjectId(?string $subjectId): void
    {
        $this->subjectId = $subjectId;
    }

    public function getStatus(): IssueStatus
    {
        return $this->status;
    }

    public function setStatus(IssueStatus $status): void
    {
        $this->status = $status;
    }

    public function getFirstDetectedAt(): ?\DateTimeInterface
    {
        return $this->firstDetectedAt;
    }

    public function setFirstDetectedAt(?\DateTimeInterface $firstDetectedAt): void
    {
        $this->firstDetectedAt = $firstDetectedAt;
    }

    public function getLastDetectedAt(): ?\DateTimeInterface
    {
        return $this->lastDetectedAt;
    }

    public function setLastDetectedAt(?\DateTimeInterface $lastDetectedAt): void
    {
        $this->lastDetectedAt = $lastDetectedAt;
    }

    public function getOccurrenceCount(): int
    {
        return $this->occurrenceCount;
    }

    public function setOccurrenceCount(int $occurrenceCount): void
    {
        $this->occurrenceCount = $occurrenceCount;
    }

    public function incrementOccurrenceCount(): void
    {
        ++$this->occurrenceCount;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }
}
