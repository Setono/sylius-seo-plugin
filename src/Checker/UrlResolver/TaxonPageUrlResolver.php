<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * Resolves a product listing (taxon) page for a representative taxon: the one referenced by the
 * page's sample resource id (a taxon code), or the first available taxon when none is set.
 */
final class TaxonPageUrlResolver extends AbstractPageUrlResolver
{
    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        ChannelUrlGeneratorInterface $urlGenerator,
        private readonly TaxonRepositoryInterface $taxonRepository,
    ) {
        parent::__construct($urlGenerator);
    }

    public function getType(): string
    {
        return 'taxon';
    }

    public function resolve(PageInterface $page): string
    {
        $taxon = $this->resolveTaxon($page);

        $localeCode = $this->urlGenerator->localeCode($page);
        if (null !== $localeCode) {
            $taxon->setCurrentLocale($localeCode);
        }

        return $this->urlGenerator->generate($page, 'sylius_shop_product_index', ['slug' => $taxon->getSlug()]);
    }

    private function resolveTaxon(PageInterface $page): TaxonInterface
    {
        $code = $page->getSampleResourceId();
        $criteria = (null !== $code && '' !== $code) ? ['code' => $code] : [];

        $taxon = $this->taxonRepository->findOneBy($criteria);
        if ($taxon instanceof TaxonInterface) {
            return $taxon;
        }

        if ([] !== $criteria) {
            throw new \RuntimeException(sprintf('No taxon found with code "%s" for page "%s".', (string) $code, (string) $page->getName()));
        }

        throw new \RuntimeException(sprintf('Unable to resolve a sample taxon for page "%s".', (string) $page->getName()));
    }
}
