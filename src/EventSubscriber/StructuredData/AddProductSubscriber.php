<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\StructuredData;

use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\StructuredData\StructuredDataContainerInterface;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product\ProductGroup;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class AddProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly StructuredDataContainerInterface $linkedDataContainer,
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

        $productGroup = new ProductGroup();
        $this->productGroupDataMapper->map($product, $productGroup);

        $this->linkedDataContainer->set($productGroup);
    }
}
