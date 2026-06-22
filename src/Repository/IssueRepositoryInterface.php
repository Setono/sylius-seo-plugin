<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Repository;

use Setono\SyliusSEOPlugin\Model\IssueInterface;
use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of IssueInterface
 * @extends RepositoryInterface<T>
 */
interface IssueRepositoryInterface extends RepositoryInterface
{
    public function findOneByFingerprint(string $fingerprint): ?IssueInterface;

    /**
     * @return list<IssueInterface>
     */
    public function findByPage(PageInterface $page): array;
}
