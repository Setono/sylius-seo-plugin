# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Sylius plugin that adds SEO features to Sylius e-commerce stores, including:
- Schema.org data generation (using spatie/schema-org)
- robots.txt file creation and management

The plugin is built as a Symfony bundle and follows Sylius plugin conventions.

## Development Commands

### Testing
```bash
# Run all tests
composer phpunit

# Run tests with coverage
vendor/bin/phpunit --coverage-clover=.build/logs/clover.xml

# Run mutation tests
vendor/bin/infection
```

### Code Quality
```bash
# Check coding standards
composer check-style

# Fix coding standards automatically
composer fix-style

# Run static analysis
composer analyse
# or
vendor/bin/psalm

# Run Rector (automated refactoring)
vendor/bin/rector process --dry-run
```

### Linting
```bash
# Lint YAML files (must be run from test application)
cd tests/Application && bin/console lint:yaml ../../src/Resources

# Lint Twig files (must be run from test application)
cd tests/Application && bin/console lint:twig ../../src/Resources

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

## Architecture

### Core Components

#### Data Mappers
The plugin uses a composite pattern for mapping Sylius entities to Schema.org types. Each Schema.org type has:
- An interface (e.g., `ProductDataMapperInterface`)
- A composite mapper (e.g., `CompositeProductDataMapper`)
- Multiple concrete implementations

The composite mappers are registered via compiler passes in `SetonoSyliusSEOPlugin::build()` with the tag pattern:
- `setono_sylius_seo.online_store_data_mapper`
- `setono_sylius_seo.product_data_mapper`
- `setono_sylius_seo.product_group_data_mapper`
- `setono_sylius_seo.website_data_mapper`

Data mappers receive domain entities (e.g., `ProductVariantInterface`) and populate Schema.org objects (e.g., `Spatie\SchemaOrg\Product`).

#### Events
The plugin dispatches events when entities are added to the Schema.org graph:
- `OnlineStoreAddedToGraph` - when an online store is added
- `ProductAddedToGraph` - when a product is added
- `ProductGroupAddedToGraph` - when a product group is added
- `WebsiteAddedToGraph` - when a website is added

These events allow extending the Schema.org data via event subscribers.

#### Twig Integration
- `JsonLdExtension` - provides Twig functions for rendering JSON-LD
- `RobotsTxtExtension` - provides Twig functions for robots.txt rendering
- `JsonLdRuntime` - runtime helper for JSON-LD generation

#### URL Generation
`ProductVariantUrlGeneratorInterface` - generates canonical URLs for product variants

#### Rendering
`RobotsTxtRendererInterface` - renders robots.txt content

### Test Application

The `tests/Application` directory contains a full Sylius application used for integration testing. It has its own:
- Kernel (`tests/Application/Kernel.php`)
- Configuration (`tests/Application/config/`)
- Console (`tests/Application/bin/console`)
- Entity extensions (`tests/Application/Entity/`)

This is where you run Symfony console commands for testing.

### Service Configuration

Services are defined in `src/Resources/config/services.xml` with additional configurations in the `services/` subdirectory.

Routes are defined in `src/Resources/config/routes.yaml` and imported into Sylius applications.

### Helper Functions

The plugin provides global helper functions in `src/Resources/functions.php`:
- `formatAmount(?int $amount): float` - formats money amounts (divides by 100)
- `sanitizeString(?string $string, bool $stripTags = true, int $maxLength = null): ?string` - sanitizes strings for Schema.org

## Channel Interface Implementation

Users must implement `Setono\SyliusSEOPlugin\Model\ChannelInterface` in their Channel entity and use the `ChannelTrait` to add SEO-related properties. This is documented in the README.md.

## Code Style

The project uses:
- Sylius Labs coding standard (via ECS)
- Psalm for static analysis (PHP 8.1+)
- Rector for automated refactoring (target: PHP 8.1)
- Infection for mutation testing

Paths to analyze: `src/` and `tests/` (excluding `tests/Application/node_modules/**` and `tests/Application/var/**`)

## PHP Version

Minimum PHP 8.1, tested on PHP 8.1 and 8.2.

## Symfony/Sylius Versions

- Symfony: 6.4 or 7.0
- Sylius: ~1.12.13
