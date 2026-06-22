<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

/**
 * Resolves the product show page for a representative product: the one referenced by the page's
 * sample resource id (a product code), or the latest product in the channel when none is set.
 */
final class ProductPageUrlResolver extends AbstractPageUrlResolver
{
    /**
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        ChannelUrlGeneratorInterface $urlGenerator,
        private readonly ProductRepositoryInterface $productRepository,
    ) {
        parent::__construct($urlGenerator);
    }

    public function getType(): string
    {
        return 'product';
    }

    public function resolve(PageInterface $page): string
    {
        $product = $this->resolveProduct($page);

        $localeCode = $this->urlGenerator->localeCode($page);
        if (null !== $localeCode) {
            $product->setCurrentLocale($localeCode);
        }

        return $this->urlGenerator->generate($page, 'sylius_shop_product_show', ['slug' => $product->getSlug()]);
    }

    private function resolveProduct(PageInterface $page): ProductInterface
    {
        $code = $page->getSampleResourceId();
        if (null !== $code && '' !== $code) {
            $product = $this->productRepository->findOneByCode($code);
            if (null === $product) {
                throw new \RuntimeException(sprintf('No product found with code "%s" for page "%s".', $code, (string) $page->getName()));
            }

            return $product;
        }

        $channel = $page->getChannel();
        $localeCode = $this->urlGenerator->localeCode($page);
        if ($channel instanceof ChannelInterface && null !== $localeCode) {
            $products = $this->productRepository->findLatestByChannel($channel, $localeCode, 1);
            $product = $products[0] ?? null;
            if ($product instanceof ProductInterface) {
                return $product;
            }
        }

        $product = $this->productRepository->findOneBy(['enabled' => true]);
        if ($product instanceof ProductInterface) {
            return $product;
        }

        throw new \RuntimeException(sprintf('Unable to resolve a sample product for page "%s".', (string) $page->getName()));
    }
}
