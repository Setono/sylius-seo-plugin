<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\OnlineStore;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\CompositeOnlineStoreDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface;
use Spatie\SchemaOrg\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;

final class CompositeOnlineStoreDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_calls_all_registered_mappers(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $onlineStore = new OnlineStore();

        $mapper1 = $this->prophesize(OnlineStoreDataMapperInterface::class);
        $mapper1->map($channel->reveal(), $onlineStore)->shouldBeCalled();

        $mapper2 = $this->prophesize(OnlineStoreDataMapperInterface::class);
        $mapper2->map($channel->reveal(), $onlineStore)->shouldBeCalled();

        $compositeMapper = new CompositeOnlineStoreDataMapper();
        $compositeMapper->add($mapper1->reveal());
        $compositeMapper->add($mapper2->reveal());

        $compositeMapper->map($channel->reveal(), $onlineStore);
    }

    #[Test]
    public function it_logs_error_when_mapper_throws_exception(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $onlineStore = new OnlineStore();

        $mapper = $this->prophesize(OnlineStoreDataMapperInterface::class);
        $mapper->map($channel->reveal(), $onlineStore)
            ->willThrow(new \RuntimeException('Test exception'));

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error(
            Argument::containingString('There was an error mapping the object'),
            Argument::type('array'),
        )->shouldBeCalled();

        $compositeMapper = new CompositeOnlineStoreDataMapper();
        $compositeMapper->setLogger($logger->reveal());
        $compositeMapper->add($mapper->reveal());

        $compositeMapper->map($channel->reveal(), $onlineStore);
    }

    #[Test]
    public function it_works_with_no_mappers(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $onlineStore = new OnlineStore();

        $compositeMapper = new CompositeOnlineStoreDataMapper();
        $compositeMapper->map($channel->reveal(), $onlineStore);

        $this->expectNotToPerformAssertions();
    }
}
