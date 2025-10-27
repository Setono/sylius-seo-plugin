<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\StructuredData;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\Event\ProductAddedToGraph;
use Setono\SyliusSEOPlugin\Event\ProductGroupAddedToGraph;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Product;
use Spatie\SchemaOrg\ProductGroup;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class AddProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Graph $graph,
        private readonly ProductDataMapperInterface $productDataMapper,
        private readonly ProductGroupDataMapperInterface $productGroupDataMapper,
        private readonly EventDispatcherInterface $eventDispatcher,
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
        $variant = $product->getEnabledVariants()->first();
        if (!$variant instanceof ProductVariantInterface) {
            return;
        }

        $graphProduct = $this->graph->product();
        Assert::isInstanceOf($graphProduct, Product::class);

        $this->productDataMapper->map($variant, $graphProduct);

        $this->eventDispatcher->dispatch(new ProductAddedToGraph($graphProduct, $product, $variant));
    }

    private function handleConfigurable(ProductInterface $product): void
    {
        $graphProductGroup = $this->graph->productGroup();
        Assert::isInstanceOf($graphProductGroup, ProductGroup::class);

        $this->productGroupDataMapper->map($product, $graphProductGroup);

        $this->eventDispatcher->dispatch(new ProductGroupAddedToGraph($graphProductGroup, $product));
    }
}
