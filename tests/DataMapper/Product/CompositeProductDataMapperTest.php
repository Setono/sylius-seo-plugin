<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\Product;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Setono\SyliusSEOPlugin\DataMapper\Product\CompositeProductDataMapper;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CompositeProductDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_calls_all_registered_mappers(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $product = new Product();

        $mapper1 = $this->prophesize(ProductDataMapperInterface::class);
        $mapper1->map($productVariant->reveal(), $product)->shouldBeCalled();

        $mapper2 = $this->prophesize(ProductDataMapperInterface::class);
        $mapper2->map($productVariant->reveal(), $product)->shouldBeCalled();

        $compositeMapper = new CompositeProductDataMapper();
        $compositeMapper->add($mapper1->reveal());
        $compositeMapper->add($mapper2->reveal());

        $compositeMapper->map($productVariant->reveal(), $product);
    }

    #[Test]
    public function it_logs_error_when_mapper_throws_exception(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $product = new Product();

        $mapper = $this->prophesize(ProductDataMapperInterface::class);
        $mapper->map($productVariant->reveal(), $product)
            ->willThrow(new \RuntimeException('Test exception'));

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error(
            Argument::containingString('There was an error mapping the object'),
            Argument::type('array'),
        )->shouldBeCalled();

        $compositeMapper = new CompositeProductDataMapper();
        $compositeMapper->setLogger($logger->reveal());
        $compositeMapper->add($mapper->reveal());

        $compositeMapper->map($productVariant->reveal(), $product);
    }

    #[Test]
    public function it_works_with_no_mappers(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $product = new Product();

        $compositeMapper = new CompositeProductDataMapper();
        $compositeMapper->map($productVariant->reveal(), $product);

        $this->expectNotToPerformAssertions();
    }
}
