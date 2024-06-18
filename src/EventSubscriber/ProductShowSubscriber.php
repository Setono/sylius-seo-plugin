<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber;

use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\LinkedData\DTO\ProductGroup;
use Setono\SyliusSEOPlugin\LinkedData\LinkedDataContainerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class ProductShowSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LinkedDataContainerInterface $linkedDataContainer,
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
        /** @var ProductInterface|mixed $product */
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        $productGroup = new ProductGroup();
        $this->productGroupDataMapper->map($product, $productGroup);

        $this->linkedDataContainer->set($productGroup);
    }
}
