<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Image;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class AddProductInformationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProductImagesResolverInterface $productImagesResolver,
        private readonly OpenGraph $openGraph,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => 'add',
        ];
    }

    public function add(ResourceControllerEvent $event): void
    {
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        $images = $this->productImagesResolver->resolve($product);
        if ([] === $images) {
            return;
        }

        foreach ($images as $image) {
            $this->openGraph->image(Image::create($image));
        }
    }
}
