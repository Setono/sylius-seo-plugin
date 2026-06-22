<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;

/**
 * A user-defined page to test. It resolves to a concrete URL (via a registered page type and an
 * optional sample resource) which is fetched and run through the assigned checks.
 *
 * @phpstan-type CheckAssignment array{id: string, code: string, config: array<string, mixed>}
 */
interface PageInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(?string $localeCode): void;

    /**
     * The page type code, selecting which URL resolver builds the URL (e.g. "homepage", "product").
     */
    public function getType(): ?string;

    public function setType(?string $type): void;

    /**
     * Route name used by the "custom_route" page type.
     */
    public function getRouteName(): ?string;

    public function setRouteName(?string $routeName): void;

    /**
     * Static route parameters used by the "custom_route" page type.
     *
     * @return array<string, mixed>
     */
    public function getRouteParameters(): array;

    /**
     * @param array<string, mixed> $routeParameters
     */
    public function setRouteParameters(array $routeParameters): void;

    /**
     * The representative resource for dynamic page types. When null the resolver auto-picks one.
     */
    public function getSampleResourceId(): ?string;

    public function setSampleResourceId(?string $sampleResourceId): void;

    /**
     * The list of check assignments to run on this page.
     *
     * @return list<CheckAssignment>
     */
    public function getChecks(): array;

    /**
     * @param list<CheckAssignment> $checks
     */
    public function setChecks(array $checks): void;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;
}
