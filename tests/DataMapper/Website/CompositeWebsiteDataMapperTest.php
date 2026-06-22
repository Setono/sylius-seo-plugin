<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\Website;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Setono\SyliusSEOPlugin\DataMapper\Website\CompositeWebsiteDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapperInterface;
use Spatie\SchemaOrg\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;

final class CompositeWebsiteDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_calls_all_registered_mappers(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $webSite = new WebSite();

        $mapper1 = $this->prophesize(WebsiteDataMapperInterface::class);
        $mapper1->map($channel->reveal(), $webSite)->shouldBeCalled();

        $mapper2 = $this->prophesize(WebsiteDataMapperInterface::class);
        $mapper2->map($channel->reveal(), $webSite)->shouldBeCalled();

        $compositeMapper = new CompositeWebsiteDataMapper();
        $compositeMapper->add($mapper1->reveal());
        $compositeMapper->add($mapper2->reveal());

        $compositeMapper->map($channel->reveal(), $webSite);
    }

    #[Test]
    public function it_logs_error_when_mapper_throws_exception(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $webSite = new WebSite();

        $mapper = $this->prophesize(WebsiteDataMapperInterface::class);
        $mapper->map($channel->reveal(), $webSite)
            ->willThrow(new \RuntimeException('Test exception'));

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error(
            Argument::containingString('There was an error mapping the object'),
            Argument::type('array'),
        )->shouldBeCalled();

        $compositeMapper = new CompositeWebsiteDataMapper();
        $compositeMapper->setLogger($logger->reveal());
        $compositeMapper->add($mapper->reveal());

        $compositeMapper->map($channel->reveal(), $webSite);
    }

    #[Test]
    public function it_works_with_no_mappers(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $webSite = new WebSite();

        $compositeMapper = new CompositeWebsiteDataMapper();
        $compositeMapper->map($channel->reveal(), $webSite);

        $this->expectNotToPerformAssertions();
    }
}
