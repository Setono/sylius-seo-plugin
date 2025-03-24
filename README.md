# Sylius SEO Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

The intention of this plugin is to add all the missing SEO features to your Sylius store.

For now this plugin has:

- [x] Schema.org data generation
- [x] robots.txt file creation and management

## Installation

```shell
composer require setono/sylius-seo-plugin
```

### Import routing

```yaml
# config/routes/setono_sylius_seo.yaml
setono_sylius_seo:
    resource: "@SetonoSyliusSEOPlugin/Resources/config/routes.yaml"
```

### Implement `ChannelInterface`

```php
<?php
declare(strict_types=1);

namespace App\Entity\Channel;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusSEOPlugin\Model\ChannelInterface;
use Setono\SyliusSEOPlugin\Model\ChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements ChannelInterface
{
    use ChannelTrait;
}
```

### Update your database schema

```shell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate -n
```

[ico-version]: https://poser.pugx.org/setono/sylius-seo-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-seo-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-seo-plugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-seo-plugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-seo-plugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-seo-plugin
[link-github-actions]: https://github.com/Setono/sylius-seo-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-seo-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-seo-plugin/master
