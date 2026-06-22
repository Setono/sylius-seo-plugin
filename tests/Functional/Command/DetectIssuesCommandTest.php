<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Functional\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Setono\SyliusSEOPlugin\Model\Issue;
use Setono\SyliusSEOPlugin\Model\IssueStatus;
use Setono\SyliusSEOPlugin\Model\Page;
use Setono\SyliusSEOPlugin\Repository\IssueRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Webmozart\Assert\Assert;

final class DetectIssuesCommandTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        Assert::isInstanceOf($entityManager, EntityManagerInterface::class);
        $this->entityManager = $entityManager;

        $this->entityManager->createQuery('DELETE FROM ' . Issue::class . ' i')->execute();
        $this->entityManager->createQuery('DELETE FROM ' . Page::class . ' p')->execute();
    }

    #[Test]
    public function it_completes_when_there_are_no_pages(): void
    {
        $tester = $this->commandTester();
        $tester->execute([]);

        $tester->assertCommandIsSuccessful();
        self::assertStringContainsString('SEO checks completed', $tester->getDisplay());
    }

    #[Test]
    public function it_detects_and_persists_an_issue_for_an_unreachable_page(): void
    {
        $page = new Page();
        $page->setName('Homepage');
        $page->setType('homepage');
        $page->setLocaleCode('en_US');
        $page->setChecks([['id' => '1', 'code' => 'http_status_ok', 'config' => []]]);
        $this->entityManager->persist($page);
        $this->entityManager->flush();

        $this->commandTester()->execute([]);

        $repository = self::getContainer()->get(IssueRepositoryInterface::class);
        Assert::isInstanceOf($repository, IssueRepositoryInterface::class);

        $issues = $repository->findByPage($page);
        self::assertNotEmpty($issues);
        self::assertSame('http_status_ok', $issues[0]->getCheck());
        self::assertSame(IssueStatus::Open, $issues[0]->getStatus());
    }

    private function commandTester(): CommandTester
    {
        $kernel = self::$kernel;
        Assert::notNull($kernel);

        $application = new Application($kernel);

        return new CommandTester($application->find('setono:sylius-seo:detect-issues'));
    }
}
