<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\StructuredData;

use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\StructuredData\StructuredDataContainerInterface;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product\ProductGroup;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class AddProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly StructuredDataContainerInterface $structuredDataContainer,
        private readonly ProductDataMapperInterface $productDataMapper,
        private readonly ProductGroupDataMapperInterface $productGroupDataMapper,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => 'populate',
        ];
    }

    public function populate(ResourceControllerEvent $event): void
    {
        /** @var ProductInterface $product */
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        $product->isSimple() ? $this->handleSimple($product) : $this->handleConfigurable($product);
    }

    private function handleSimple(ProductInterface $product): void
    {
        $variant = $product->getvariants()->first();
        if (!$variant instanceof ProductVariantInterface) {
            return;
        }

        $dto = new Product();
        $this->productDataMapper->map($variant, $dto);
        $this->structuredDataContainer->set($dto);
    }

    private function handleConfigurable(ProductInterface $product): void
    {
        $dto = new ProductGroup();
        $this->productGroupDataMapper->map($product, $dto);
        $this->structuredDataContainer->set($dto);
    }
}
