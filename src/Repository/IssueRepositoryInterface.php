<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Repository;

use Setono\SyliusSEOPlugin\Model\IssueInterface;
use Setono\SyliusSEOPlugin\Model\PageInterface;

interface IssueRepositoryInterface
{
    public function findOneById(int $id): ?IssueInterface;

    public function findOneByFingerprint(string $fingerprint): ?IssueInterface;

    /**
     * @return list<IssueInterface>
     */
    public function findByPage(PageInterface $page): array;
}
