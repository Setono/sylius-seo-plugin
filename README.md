# Sylius SEO Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

[Setono](https://setono.com) have made a bunch of [plugins for Sylius](https://github.com/Setono?q=plugin&sort=stargazers), and we have some guidelines
which we try to follow when developing plugins. These guidelines are used in this repository, and it gives you a very
solid base when developing plugins.

Enjoy! 

## Quickstart

1. Run
    ```shell
    composer create-project --prefer-source --no-install --remove-vcs setono/sylius-seo-plugin:1.12.x-dev ProjectName
    ``` 
    or just click the `Use this template` button at the right corner of this repository.
2. Run
   ```shell
   cd ProjectName && composer install
   ```
3. From the plugin skeleton root directory, run the following commands:

    ```bash
    php init
    (cd tests/Application && yarn install)
    (cd tests/Application && yarn build)
    (cd tests/Application && bin/console assets:install)
    
    (cd tests/Application && bin/console doctrine:database:create)
    (cd tests/Application && bin/console doctrine:schema:create)
   
    (cd tests/Application && bin/console sylius:fixtures:load -n)
    ```
   
4. Start your local PHP server: `symfony serve` (see https://symfony.com/doc/current/setup/symfony_server.html for docs)

To be able to set up a plugin's database, remember to configure you database credentials in `tests/Application/.env` and `tests/Application/.env.test`.

[ico-version]: https://poser.pugx.org/setono/sylius-seo-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-seo-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-seo-plugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-seo-plugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2FSyliusPluginSkeleton%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-seo-plugin
[link-github-actions]: https://github.com/Setono/sylius-seo-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-seo-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-seo-plugin/master
