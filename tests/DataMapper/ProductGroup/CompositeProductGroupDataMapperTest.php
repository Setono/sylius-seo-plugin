<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\ProductGroup;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\CompositeProductGroupDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;

final class CompositeProductGroupDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calls_all_registered_mappers(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $productGroup = new ProductGroup();

        $mapper1 = $this->prophesize(ProductGroupDataMapperInterface::class);
        $mapper1->map($product->reveal(), $productGroup)->shouldBeCalled();

        $mapper2 = $this->prophesize(ProductGroupDataMapperInterface::class);
        $mapper2->map($product->reveal(), $productGroup)->shouldBeCalled();

        $compositeMapper = new CompositeProductGroupDataMapper();
        $compositeMapper->add($mapper1->reveal());
        $compositeMapper->add($mapper2->reveal());

        $compositeMapper->map($product->reveal(), $productGroup);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logs_error_when_mapper_throws_exception(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $productGroup = new ProductGroup();

        $mapper = $this->prophesize(ProductGroupDataMapperInterface::class);
        $mapper->map($product->reveal(), $productGroup)
            ->willThrow(new \RuntimeException('Test exception'));

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error(
            Argument::containingString('There was an error mapping the object'),
            Argument::type('array'),
        )->shouldBeCalled();

        $compositeMapper = new CompositeProductGroupDataMapper();
        $compositeMapper->setLogger($logger->reveal());
        $compositeMapper->add($mapper->reveal());

        $compositeMapper->map($product->reveal(), $productGroup);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_no_mappers(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $productGroup = new ProductGroup();

        $compositeMapper = new CompositeProductGroupDataMapper();
        $compositeMapper->map($product->reveal(), $productGroup);

        $this->expectNotToPerformAssertions();
    }
}
