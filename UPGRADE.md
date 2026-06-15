# Upgrade

## From 1.x to 2.x

Version 2 of this plugin targets **Sylius 2.x**. If you are still on Sylius 1.x, stay on the `1.x`
branch of this plugin.

### Requirements

The minimum requirements changed:

| Dependency | 1.x          | 2.x                  |
|------------|--------------|----------------------|
| PHP        | `>=8.1`      | `>=8.2`              |
| Symfony    | `^6.4`       | `^6.4 \|\| ^7.4`     |
| Sylius     | `~1.12`      | `^2.0`               |
| Twig       | `^2 \|\| ^3` | `^3.0`               |

### Routing import path changed

The plugin's files moved out of `src/Resources/` to the package root (`config/`, `templates/`,
`translations/`). Update the routing import in your application:

```diff
 # config/routes/setono_sylius_seo.yaml
 setono_sylius_seo:
-    resource: "@SetonoSyliusSEOPlugin/Resources/config/routes.yaml"
+    resource: "@SetonoSyliusSEOPlugin/config/routes.yaml"
```

### Template injection now uses Twig Hooks

Sylius 2 replaced the `sylius_ui` event-based template injection with
[Twig Hooks](https://github.com/Sylius/TwigHooks). The plugin now registers its templates through
`sylius_twig_hooks` on these hooks:

| Output                       | Hook                                                                                            |
|------------------------------|-------------------------------------------------------------------------------------------------|
| JSON-LD + Open Graph (`<head>`) | `sylius_shop.base#metatags`                                                                  |
| robots.txt field (channel form) | `sylius_admin.channel.create.content.form.sections`, `sylius_admin.channel.update.content.form.sections` |

If you previously overrode the plugin's blocks via `sylius_ui` configuration, migrate those overrides
to the corresponding Twig hooks.

### Renamed template

The admin channel partial was renamed (leading-underscore partials are a Sylius 1 convention) and now
renders the field through the Twig hook context:

```diff
-@SetonoSyliusSEOPlugin/admin/channel/_robots_txt.html.twig
+@SetonoSyliusSEOPlugin/admin/channel/robots_txt.html.twig
```

If you overrode this template in your application, rename your override accordingly. The markup was
also migrated from Semantic UI to Bootstrap 5 to match the Sylius 2 admin.

### Channel entity: use attribute mapping

Doctrine ORM 3 ignores docblock (`@ORM\*`) mappings. If your `Channel` entity used annotations,
switch to attributes:

```diff
-/**
- * @ORM\Entity
- * @ORM\Table(name="sylius_channel")
- */
+#[ORM\Entity]
+#[ORM\Table(name: 'sylius_channel')]
 class Channel extends BaseChannel implements ChannelInterface
 {
     use ChannelTrait;
 }
```

### Service configuration is now PHP

The bundle's service configuration was converted from XML to the PHP DSL
(`config/services.php` + `config/services/`). This is internal and only relevant if you referenced
the plugin's configuration files directly.
