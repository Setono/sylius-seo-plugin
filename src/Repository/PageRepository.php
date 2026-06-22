<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Repository;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class PageRepository extends EntityRepository implements PageRepositoryInterface
{
    public function findOneById(int $id): ?PageInterface
    {
        $page = $this->find($id);
        Assert::nullOrIsInstanceOf($page, PageInterface::class);

        return $page;
    }

    public function findEnabled(?ChannelInterface $channel = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->andWhere('o.enabled = true')
            ->orderBy('o.id', 'ASC')
        ;

        if (null !== $channel) {
            $queryBuilder
                ->andWhere('o.channel = :channel')
                ->setParameter('channel', $channel)
            ;
        }

        $pages = $queryBuilder->getQuery()->getResult();
        Assert::isArray($pages);
        Assert::allIsInstanceOf($pages, PageInterface::class);

        return array_values($pages);
    }
}
