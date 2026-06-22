<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Repository;

use Setono\SyliusSEOPlugin\Model\IssueInterface;
use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Webmozart\Assert\Assert;

/**
 * @template T of IssueInterface
 * @implements IssueRepositoryInterface<T>
 */
class IssueRepository extends EntityRepository implements IssueRepositoryInterface
{
    public function findOneByFingerprint(string $fingerprint): ?IssueInterface
    {
        $issue = $this->findOneBy(['fingerprint' => $fingerprint]);
        Assert::nullOrIsInstanceOf($issue, IssueInterface::class);

        return $issue;
    }

    public function findByPage(PageInterface $page): array
    {
        $issues = $this->findBy(['page' => $page]);
        Assert::allIsInstanceOf($issues, IssueInterface::class);

        return array_values($issues);
    }
}
