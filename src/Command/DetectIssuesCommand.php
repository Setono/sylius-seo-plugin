<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Command;

use Setono\SyliusSEOPlugin\Checker\CheckRunnerInterface;
use Setono\SyliusSEOPlugin\Model\PageInterface;
use Setono\SyliusSEOPlugin\Repository\PageRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: 'setono:sylius-seo:detect-issues',
    description: 'Runs the configured SEO checks against your pages and stores the detected issues',
)]
final class DetectIssuesCommand extends Command
{
    /**
     * @param PageRepositoryInterface<PageInterface> $pageRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private readonly CheckRunnerInterface $checkRunner,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly ChannelRepositoryInterface $channelRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('channel', null, InputOption::VALUE_REQUIRED, 'Limit detection to the channel with this code')
            ->addOption('page', null, InputOption::VALUE_REQUIRED, 'Run a single page by its id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $pageId = $input->getOption('page');
        Assert::nullOrString($pageId);

        if (null !== $pageId) {
            $page = $this->pageRepository->find((int) $pageId);
            if (null === $page) {
                $io->error(sprintf('No page found with id "%s"', $pageId));

                return Command::FAILURE;
            }

            $this->checkRunner->run($page);
            $io->success(sprintf('Ran SEO checks for page "%s"', (string) $page->getName()));

            return Command::SUCCESS;
        }

        $channel = null;
        $channelCode = $input->getOption('channel');
        Assert::nullOrString($channelCode);

        if (null !== $channelCode) {
            $channel = $this->channelRepository->findOneBy(['code' => $channelCode]);
            if (!$channel instanceof ChannelInterface) {
                $io->error(sprintf('No channel found with code "%s"', $channelCode));

                return Command::FAILURE;
            }
        }

        $this->checkRunner->runAll($channel);
        $io->success('SEO checks completed');

        return Command::SUCCESS;
    }
}
