<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\StructuredData;

use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\ItemList;
use Spatie\SchemaOrg\Schema;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class AddItemListSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Graph $graph,
        private readonly ProductDataMapperInterface $productDataMapper,
        private readonly ProductVariantResolverInterface $productVariantResolver,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.index' => 'populate',
        ];
    }

    public function populate(ResourceControllerEvent $event): void
    {
        $grid = $event->getSubject();

        if (!$grid instanceof ResourceGridView) {
            return;
        }

        $products = [];
        foreach ($grid->getData() as $productEntity) {
            if (!$productEntity instanceof ProductInterface) {
                continue;
            }

            $variant = $this->productVariantResolver->getVariant($productEntity);
            if (!$variant instanceof ProductVariantInterface) {
                continue;
            }

            $product = Schema::product();
            $this->productDataMapper->map($variant, $product);

            $products[] = $product;
        }

        $itemList = $this->graph->itemList();
        Assert::isInstanceOf($itemList, ItemList::class);

        $itemList->itemListElement($products);
    }
}
