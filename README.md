# Sylius SEO Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Add the SEO features that Sylius is missing — structured data, Open Graph metadata and a manageable
`robots.txt` — with sensible defaults and zero template changes.

Once installed, the plugin automatically enriches your storefront's `<head>` on every page, so search
engines and social networks understand your catalog out of the box.

## Features

- **🔎 Schema.org structured data (JSON-LD)** — automatically generates and renders structured data
  for your storefront:
  - `OnlineStore` and `WebSite` on the homepage (the `WebSite` node can expose a
    [Sitelinks Search Box](https://developers.google.com/search/docs/appearance/structured-data/sitelinks-searchbox))
  - `Product` / `ProductGroup` on product pages, including images and offers (price, availability,
    canonical variant URLs)

  The data is rendered as a single JSON-LD graph in the shop `<head>`.
- **📣 Open Graph metadata** — emits `<meta property="og:*">` tags (title, description, site name,
  locale, images, …) for rich link previews on social platforms, populated from your channel and
  products.
- **🤖 robots.txt management** — edit a `robots.txt` per channel straight from the admin panel and
  serve it at `/robots.txt`. The content is rendered as a Twig template, so it can be dynamic.
- **🩺 SEO checks** — define the pages you want to monitor in the admin, fetch them over
  HTTP, and run them through a battery of generic checks (HTTP status, title, meta description, H1,
  canonical, robots/noindex, Open Graph, JSON-LD, mixed content, …). Detected problems are listed in
  an admin grid with severities and can be ignored. You can even define ad-hoc checks from the admin
  (assert any CSS/XPath selector's content, including a JSON path into a JSON-LD block) — no code.
- **🧩 Extensible by design** — add or override any fact in the structured data with a tagged data
  mapper, or hook into the dedicated events. Nothing is hardcoded.
- **🌍 Translations** — the admin UI ships with translations for 16 locales.

## Requirements

| Package | Version          |
|---------|------------------|
| PHP     | `>= 8.2`         |
| Symfony | `6.4` or `7.4`   |
| Sylius  | `^2.0`           |

> Using Sylius 1.x? Use the [`1.x`](https://github.com/Setono/sylius-seo-plugin/tree/1.x) branch
> (`^1.0`) instead.

## Installation

### 1. Require the plugin

```shell
composer require setono/sylius-seo-plugin
```

### 2. Register the plugin

```php
# config/bundles.php

return [
    // ...
    Setono\SyliusSEOPlugin\SetonoSyliusSEOPlugin::class => ['all' => true],
];
```

### 3. Import the routing

```yaml
# config/routes/setono_sylius_seo.yaml
setono_sylius_seo:
    resource: "@SetonoSyliusSEOPlugin/config/routes.yaml"
```

### 4. Implement `ChannelInterface`

The plugin stores the `robots.txt` content on your channel. Implement the plugin's `ChannelInterface`
and use the `ChannelTrait` on your Channel entity:

```php
<?php
declare(strict_types=1);

namespace App\Entity\Channel;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusSEOPlugin\Model\ChannelInterface;
use Setono\SyliusSEOPlugin\Model\ChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_channel')]
class Channel extends BaseChannel implements ChannelInterface
{
    use ChannelTrait;
}
```

### 5. Update your database schema

```shell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate -n
```

That's it — structured data and Open Graph tags are now rendered on your storefront automatically.

## Usage

### Structured data & Open Graph

There is nothing to wire up in your templates. The plugin injects its output into the shop `<head>`
via Sylius Twig Hooks, so visiting the homepage or a product page already produces the JSON-LD graph
and the `og:*` meta tags. Use Google's
[Rich Results Test](https://search.google.com/test/rich-results) to verify the structured data.

### robots.txt

Go to **Admin → Channels → (edit a channel)** and fill in the **Robots.txt** field. The content is
served at `/robots.txt` for that channel's domain.

> If a physical `public/robots.txt` file exists it is served by your web server and shadows this
> route. The admin form warns you when that is the case.

### SEO checks

This feature adds two database tables. To use it:

1. Import the admin routing **under your admin prefix**:

   ```yaml
   # config/routes/setono_sylius_seo_admin.yaml
   setono_sylius_seo_admin:
       resource: "@SetonoSyliusSEOPlugin/config/routes/admin.yaml"
       prefix: '/%sylius_admin.path_name%'
   ```

2. Update your database schema (the plugin ships migrations for MySQL/MariaDB and PostgreSQL):

   ```shell
   bin/console doctrine:migrations:migrate
   ```

Then, in the admin:

- Go to **Admin → SEO → Pages to test** and add the pages you care about. A page points at a Sylius
  route via a *page type* (`Homepage`, `Product`, `Taxon` or a custom route); for the dynamic types
  you can pick a representative product/taxon code, or leave it empty to auto-pick one. Tick the
  checks that should run on each page.
- Run the checks — per page with the **Run checks** row action, or all at once on the command line:

  ```shell
  bin/console setono:sylius-seo:detect-issues          # all enabled pages
  bin/console setono:sylius-seo:detect-issues --channel=FASHION_WEB
  bin/console setono:sylius-seo:detect-issues --page=42
  ```

- Review the results under **Admin → SEO → Issues**, filter by severity/status/check, and **ignore**
  the ones you don't care about. Ignored issues stay ignored across re-runs, and issues that are no
  longer detected are automatically marked as *resolved*.

Pages are fetched over real HTTP using the channel hostname (and the `https` scheme by default). When
testing locally, point the checks at your running server with `base_url` (see the configuration
below).

#### Ad-hoc checks (no code)

Two built-in checks let you assert almost anything from the admin, without writing PHP:

- **Element content** — select an element with a CSS or XPath selector, optionally read an attribute
  or extract a value with a JSON path, then assert it `contains` / `equals` / `matches` (regex) /
  `exists` / is `absent`. For example, to assert the price inside a JSON-LD block:
  selector `script#seo-ggg`, JSON path `$.offers[0].price`, assertion `equals`, value `19.99`.
- **Element exists** — assert that the number of elements matching a selector is within a range.

## Configuration

All structured data features are enabled by default. The full configuration with its defaults:

```yaml
# config/packages/setono_sylius_seo.yaml
setono_sylius_seo:
    # Service id of the canonical product variant URL generator. Override with your own implementation.
    product_variant_url_generator: 'Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGenerator'

    structured_data:
        product:
            enabled: true
        online_store:
            enabled: true
        website:
            # Disabled by default. Enable it to add a WebSite node with a Sitelinks Search Box.
            enabled: false
            search_url_template:
                route: app_shop_search # your own route handling search queries
                query_parameter: q     # the query parameter that route expects

    checks:
        # Scheme used when building page URLs from a channel hostname.
        scheme: https
        # Optional. Build all page URLs against this scheme://host[:port] instead of the channel
        # hostname. Handy for local/staging testing, e.g. 'http://127.0.0.1:8000'.
        base_url: null
```

To turn a feature off, disable it:

```yaml
setono_sylius_seo:
    structured_data:
        product:
            enabled: false
```

## Extending

### Add or override structured data

Each Schema.org type is built by a set of *data mappers*. To add a new fact, create a class
implementing the relevant interface — it is autoconfigured and picked up automatically:

| Schema.org type | Interface to implement                                                  |
|-----------------|-------------------------------------------------------------------------|
| Product         | `Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface`        |
| Product group   | `Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface` |
| Online store    | `Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface`   |
| Website         | `Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapperInterface`           |

```php
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class MpnProductDataMapper implements ProductDataMapperInterface
{
    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        // enrich the generated Schema.org Product with your own data
        $product->mpn($productVariant->getCode());
    }
}
```

### Events

After each object is added to the graph the plugin dispatches an event you can subscribe to in order
to extend or replace the generated data:

- `Setono\SyliusSEOPlugin\Event\OnlineStoreAddedToGraph`
- `Setono\SyliusSEOPlugin\Event\ProductAddedToGraph`
- `Setono\SyliusSEOPlugin\Event\ProductGroupAddedToGraph`
- `Setono\SyliusSEOPlugin\Event\WebsiteAddedToGraph`

### Add a custom SEO check

Implement `IssueDetectorInterface` — it is autoconfigured, so tagging is optional. The detector
receives a neutral `Inspection` (URL, HTTP status, all response headers, raw body and the lazily
parsed DOM) and yields `DetectedIssue` value objects. Once registered, your check becomes selectable
on every page in the admin (its label/description come from the
`setono_sylius_seo.check.<code>.{label,description}` translation keys).

```php
use Setono\SyliusSEOPlugin\Checker\DetectedIssue;
use Setono\SyliusSEOPlugin\Checker\Detector\IssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\Inspection;
use Setono\SyliusSEOPlugin\Model\Severity;

final class FaviconDetector implements IssueDetectorInterface
{
    public function getCode(): string
    {
        return 'favicon';
    }

    public function detect(Inspection $inspection, array $config = []): iterable
    {
        $crawler = $inspection->getCrawler();
        if (null !== $crawler && 0 === $crawler->filter('link[rel~="icon"]')->count()) {
            yield new DetectedIssue($this->getCode(), Severity::Notice, 'app.issue.favicon');
        }
    }
}
```

To make the check configurable from the admin, implement `ConfigurableIssueDetectorInterface` instead
and return the Symfony form type used to edit its configuration from `getConfigFormType()`.

## Contributing

```shell
composer install
composer phpunit      # run the unit tests
composer analyse      # run static analysis
composer check-style  # check the coding standard
```

See the test application in `tests/Application` for a runnable Sylius store with the plugin installed.

## License

This plugin is released under the [MIT License](LICENSE).

[ico-version]: https://poser.pugx.org/setono/sylius-seo-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-seo-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-seo-plugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-seo-plugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-seo-plugin%2F2.x

[link-packagist]: https://packagist.org/packages/setono/sylius-seo-plugin
[link-github-actions]: https://github.com/Setono/sylius-seo-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-seo-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-seo-plugin/2.x
