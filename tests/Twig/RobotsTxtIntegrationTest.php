<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Twig;

use PHPUnit\Framework\Attributes\Test;
use Setono\SyliusSEOPlugin\Twig\RobotsTxtExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Test\IntegrationTestCase;

final class RobotsTxtIntegrationTest extends IntegrationTestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir() . '/robots_txt_test_' . uniqid();
        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        $robotsFile = $this->tempDir . '/robots.txt';
        if (file_exists($robotsFile)) {
            unlink($robotsFile);
        }
        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }

        parent::tearDown();
    }

    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/Fixtures/robots_txt';
    }

    /**
     * @return list<ExtensionInterface>
     */
    protected function getExtensions(): array
    {
        return [new RobotsTxtExtension($this->tempDir)];
    }

    #[Test]
    public function it_returns_null_when_no_robots_txt_exists(): void
    {
        $extension = new RobotsTxtExtension($this->tempDir);

        self::assertNull($extension->existingRobotsTxt());
    }

    #[Test]
    public function it_returns_content_when_robots_txt_exists(): void
    {
        $content = "User-agent: *\nDisallow: /admin";
        file_put_contents($this->tempDir . '/robots.txt', $content);

        $extension = new RobotsTxtExtension($this->tempDir);

        self::assertSame($content, $extension->existingRobotsTxt());
    }
}
