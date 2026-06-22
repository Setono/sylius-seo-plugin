<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\SetonoSyliusSEOPlugin;

final class SetonoSyliusSEOPluginTest extends TestCase
{
    #[Test]
    public function it_resolves_its_path_to_the_package_root(): void
    {
        $plugin = new SetonoSyliusSEOPlugin();

        self::assertSame(\dirname(__DIR__), $plugin->getPath());
    }
}
