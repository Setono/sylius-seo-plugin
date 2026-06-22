<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Image;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Symfony\Component\String\u;
use Webmozart\Assert\Assert;

final readonly class AddProductInformationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ProductImagesResolverInterface $productImagesResolver,
        private OpenGraph $openGraph,
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
        if ([] !== $images) {
            $this->openGraph->image((new Image($images[0]))->alt($product->getName()));
        }

        $description = self::sanitizeDescription($product->getDescription());
        if (null !== $description) {
            $this->openGraph->description($description);
        }
    }

    /**
     * See https://webmasters.stackexchange.com/questions/144790/why-should-the-content-of-the-ogdescription-meta-tag-be-trimmed
     */
    private static function sanitizeDescription(?string $description): ?string
    {
        if (null === $description) {
            return null;
        }

        return u(preg_replace(['/\s+/', '/ +/'], ' ', strip_tags($description)))
            ->trim()
            ->truncate(300, '...', false)
            ->toString()
        ;
    }
}
