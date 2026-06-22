# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Sylius plugin that adds SEO features to Sylius e-commerce stores, including:
- Schema.org data generation (using spatie/schema-org)
- robots.txt file creation and management

The plugin is built as a Symfony bundle and follows Sylius plugin conventions.

## Code Standards

Follow clean code principles and SOLID design patterns when working with this codebase:
- Write clean, readable, and maintainable code
- Apply SOLID principles (Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion)
- Use meaningful variable and method names
- Keep methods and classes focused on a single responsibility
- Favor composition over inheritance
- Write code that is easy to test and extend

### Testing Requirements
- Write unit tests for all new functionality (if it makes sense)
- Follow the BDD-style naming convention for test methods (e.g., `it_should_do_something_when_condition_is_met`)
- **MUST use Prophecy for mocking** - Use the `ProphecyTrait` and `$this->prophesize()` for all mocks, NOT PHPUnit's `$this->createMock()`
- **Form testing** - Use Symfony's best practices for form testing as documented at https://symfony.com/doc/current/form/unit_testing.html
  - Extend `Symfony\Component\Form\Test\TypeTestCase` for form type tests
  - Use `$this->factory->create()` to create form instances
  - Test form submission, validation, and data transformation
- Ensure tests are isolated and don't depend on external state
- Test both happy path and edge cases

## Development Commands

### Testing
```bash
# Run all tests
composer phpunit

# Run a single test file
vendor/bin/phpunit tests/DataMapper/Product/ProductDataMapperTest.php

# Run a single test method by name (BDD method names work well here)
vendor/bin/phpunit --filter it_maps_variant_data_to_product

# Run tests with coverage
vendor/bin/phpunit --coverage-clover=.build/logs/clover.xml

# Run mutation tests
vendor/bin/infection
```

PHPUnit bootstraps through the test application (`bootstrap="tests/Application/config/bootstrap.php"`), so a working `tests/Application` (with its dependencies installed) is required to run the suite. The test suite covers `tests/` and mirrors the `src/` namespace layout (one test dir per component).

### Code Quality
```bash
# Check coding standards
composer check-style

# Fix coding standards automatically
composer fix-style

# Run static analysis
composer analyse
# or
vendor/bin/phpstan

# Run Rector (automated refactoring)
vendor/bin/rector process --dry-run
```

### Static Analysis

#### PHPStan Configuration
PHPStan is configured in `phpstan.neon` with:
- **Analysis Level**: max (strictest)
- **Extensions**: Auto-loaded via `phpstan/extension-installer`
  - `phpstan/phpstan-symfony` - Symfony framework integration
  - `phpstan/phpstan-doctrine` - Doctrine ORM integration
  - `phpstan/phpstan-phpunit` - PHPUnit test integration
  - `jangregor/phpstan-prophecy` - Prophecy mocking integration
- **Baseline**: Generate with `composer analyse -- --generate-baseline` to track improvements

### Linting
```bash
# Lint YAML files (must be run from test application)
cd tests/Application && bin/console lint:yaml ../../config ../../translations

# Lint Twig files (must be run from test application)
cd tests/Application && bin/console lint:twig ../../templates

# Lint Symfony container (must be run from test application)
cd tests/Application && bin/console lint:container
```

### Database Operations (Test Application)
```bash
cd tests/Application

# Create database
bin/console doctrine:database:create

# Create schema
bin/console doctrine:schema:create

# Validate schema
bin/console doctrine:schema:validate -vvv

# Generate migration diff
bin/console doctrine:migrations:diff

# Run migrations
bin/console doctrine:migrations:migrate -n
```

### Validation
```bash
# Validate composer.json
composer validate --strict

# Check if composer.json is normalized
composer normalize --dry-run
```

### Test Application
The plugin includes a test Symfony application in `tests/Application/` for development and testing:
- Navigate to `tests/Application/` directory
- Run `yarn install && yarn build` to build assets
- Use standard Symfony commands for the test app
- **Sylius Backend Credentials**: Username: `sylius`, Password: `sylius`

## Bash Tools Recommendations

Use the right tool for the right job when executing bash commands:

- **Finding FILES?** → Use `fd` (fast file finder)
- **Finding TEXT/strings?** → Use `rg` (ripgrep for text search)
- **Finding CODE STRUCTURE?** → Use `ast-grep` (syntax-aware code search)
- **SELECTING from multiple results?** → Pipe to `fzf` (interactive fuzzy finder)
- **Interacting with JSON?** → Use `jq` (JSON processor)
- **Interacting with YAML or XML?** → Use `yq` (YAML/XML processor)

Examples:
- `fd "*.php" | fzf` - Find PHP files and interactively select one
- `rg "function.*validate" | fzf` - Search for validation functions and select
- `ast-grep --lang php -p 'class $name extends $parent'` - Find class inheritance patterns

## Architecture

The plugin produces three independent SEO outputs, each with its own pipeline: **Schema.org structured data (JSON-LD)**, **Open Graph metadata**, and **robots.txt**.

### Structured data (Schema.org / JSON-LD) pipeline

This is the most involved subsystem. The flow, end to end:

1. **Event subscribers** in `src/EventSubscriber/StructuredData/` listen to Sylius resource controller events (e.g. `sylius.product.show`) — see `getSubscribedEvents()` in each subscriber.
2. Each subscriber asks the relevant **data mapper** to populate a `Spatie\SchemaOrg\*` object from the Sylius entity, then adds that object to the shared `Spatie\SchemaOrg\Graph` service (a single injected `Graph` instance accumulates everything for the request).
3. After adding to the graph, the subscriber dispatches a `*AddedToGraph` **event** so other code can extend/override the Schema.org object.
4. `JsonLdExtension` (Twig) renders the accumulated `Graph` as JSON-LD in the page `<head>`.

**Data mappers** use a composite pattern. Per Schema.org type there is an interface, a `Composite*` mapper, and N concrete mappers (all extending `AbstractDataMapper`). The composites are assembled at compile time: `SetonoSyliusSEOPlugin::build()` registers a `CompositeCompilerPass` (from `setono/composite-compiler-pass`) per type, collecting all services tagged with:
- `setono_sylius_seo.online_store_data_mapper`
- `setono_sylius_seo.product_data_mapper`
- `setono_sylius_seo.product_group_data_mapper`
- `setono_sylius_seo.website_data_mapper`

To add a new fact to the structured data, write a concrete mapper and tag it — the composite picks it up automatically. Concrete product mappers today: `ProductDataMapper`, `ImageProductDataMapper`, `OffersProductDataMapper` (and `HasVariantProductGroupDataMapper` for groups).

The `*AddedToGraph` events (`OnlineStoreAddedToGraph`, `ProductAddedToGraph`, `ProductGroupAddedToGraph`, `WebsiteAddedToGraph`) are the public extension point for consumers who don't want to register a mapper.

### Open Graph pipeline

Parallel in spirit to structured data but a separate hierarchy under `src/OpenGraph/`:
- `OpenGraph` is the accumulator object; `Property/` (Image, Audio, Video) and `Type/` (Article, Book, Profile, Website, and `Music/*`, `Video/*` subtypes implementing `Type\TypeInterface`) model the OG vocabulary.
- Subscribers in `src/EventSubscriber/OpenGraph/` (`AddChannelInformationSubscriber`, `AddProductInformationSubscriber`) populate it.
- `OpenGraphExtension` (Twig) renders the `<meta property="og:*">` tags.

### robots.txt pipeline

`RenderRobotsTxtAction` controller (routed via `config/routes.yaml`) → `RobotsTxtRendererInterface` / `RobotsTxtRenderer` builds the content → also exposed through `RobotsTxtExtension` (Twig).

### Template injection (Twig Hooks)

The plugin injects its templates via Sylius 2 **Twig Hooks**, configured in `SetonoSyliusSEOExtension::prepend()` through `prependExtensionConfig('sylius_twig_hooks', ...)`:
- `sylius_shop.base#metatags` → `json_ld.html.twig` + `open_graph.html.twig` (shop `<head>`)
- `sylius_admin.channel.{create,update}.content.form.sections` → `admin/channel/robots_txt.html.twig` (channel admin form; the partial reads the field via `hookable_metadata.context.form.robotsTxt`)

`ChannelTypeExtension` (a `form.type_extension`) still adds the `robotsTxt` field; the hook only renders it.

### Other components

- **URL generation** — `src/UrlGenerator/` generates canonical URLs for product variants.
- **Image resolution** — `ProductImagesResolverInterface` (with a `CachedProductImagesResolver` decorator) resolves the images used in product structured data / OG tags.
- **Channel configuration** — consumers store SEO settings on their Channel entity; see "Channel Interface Implementation" below. `ChannelTypeExtension` (a form type extension) adds the SEO fields to Sylius' channel admin form.

### Service configuration

Services are defined with the **PHP DSL** (`ContainerConfigurator`), not XML. `config/services.php` imports per-concern files from `config/services/` (`graph.php`, `open_graph.php`, `event_subscriber.php`, `form.php`, `renderer.php`, `resolver.php`, `twig.php`, `url_generator.php`, `controller.php`); the `config/services/structured_data/` files are loaded conditionally by the extension based on feature flags. When adding a service, put it in the matching file. Note: services are **not** autowired/autoconfigured by default — the plugin's data mappers are tagged explicitly (preserve the tag priorities), while `registerForAutoconfiguration()` in the extension tags consumer-provided mappers.

The bundle overrides `getPath()` to return the package root so Symfony resolves `config/`, `templates/`, and `translations/` from there (the v2 layout — no more `src/Resources/`).

Routes are defined in `config/routes.yaml` and imported into the consuming Sylius application.

### Helper Functions

The plugin provides global helper functions in `src/functions.php` (autoloaded via composer `files`):
- `formatAmount(?int $amount): float` - formats money amounts (divides by 100)
- `sanitizeString(?string $string, bool $stripTags = true, ?int $maxLength = null): ?string` - sanitizes strings for Schema.org

## Channel Interface Implementation

Users must implement `Setono\SyliusSEOPlugin\Model\ChannelInterface` in their Channel entity and use the `ChannelTrait` to add SEO-related properties. This is documented in the README.md.

## PHP Version

Minimum PHP 8.2, tested on PHP 8.2, 8.3 and 8.4.

## Symfony/Sylius Versions

- Symfony: 6.4 or 7.4
- Sylius: ^2.0 (developed/tested against ~2.2)

Symfony version resolution is pinned via the `SYMFONY_REQUIRE` env var + `symfony/flex` (the CI composite actions handle this; locally run `SYMFONY_REQUIRE='~7.4.0' composer update` with flex installed). Without pinning, transitive Symfony packages float to the latest major (8.x), which requires PHP 8.4.
