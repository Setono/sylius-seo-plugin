<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusSEOPlugin\Repository\PageRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\TimestampableTrait;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'setono_sylius_seo__page')]
class Page implements PageInterface, \Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $name = null;

    #[ORM\ManyToOne(targetEntity: ChannelInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    protected ?ChannelInterface $channel = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $localeCode = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $type = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $routeName = null;

    /** @var array<string, mixed> */
    #[ORM\Column(type: Types::JSON)]
    protected array $routeParameters = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $sampleResourceId = null;

    /** @var list<array{id: string, code: string, config: array<string, mixed>}> */
    #[ORM\Column(type: Types::JSON)]
    protected array $checks = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    protected bool $enabled = true;

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(?string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): void
    {
        $this->routeName = $routeName;
    }

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    public function setRouteParameters(array $routeParameters): void
    {
        $this->routeParameters = $routeParameters;
    }

    public function getSampleResourceId(): ?string
    {
        return $this->sampleResourceId;
    }

    public function setSampleResourceId(?string $sampleResourceId): void
    {
        $this->sampleResourceId = $sampleResourceId;
    }

    public function getChecks(): array
    {
        return $this->checks;
    }

    public function setChecks(array $checks): void
    {
        $this->checks = array_values($checks);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
