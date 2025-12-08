<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Profile;

final class ProfileTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $profile = new Profile();

        self::assertSame('profile', $profile->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_html_by_default(): void
    {
        $profile = new Profile();

        self::assertSame('', $profile->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_first_name(): void
    {
        $profile = new Profile(firstName: 'John');

        self::assertSame('<meta property="profile:first_name" content="John">', $profile->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_last_name(): void
    {
        $profile = new Profile(lastName: 'Doe');

        self::assertSame('<meta property="profile:last_name" content="Doe">', $profile->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_username(): void
    {
        $profile = new Profile(username: 'johndoe');

        self::assertSame('<meta property="profile:username" content="johndoe">', $profile->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_gender(): void
    {
        $profile = new Profile(gender: 'male');

        self::assertSame('<meta property="profile:gender" content="male">', $profile->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_all_properties(): void
    {
        $profile = new Profile(
            firstName: 'John',
            lastName: 'Doe',
            username: 'johndoe',
            gender: 'male',
        );

        $expected = '<meta property="profile:first_name" content="John">' . "\n" .
            '<meta property="profile:last_name" content="Doe">' . "\n" .
            '<meta property="profile:username" content="johndoe">' . "\n" .
            '<meta property="profile:gender" content="male">';

        self::assertSame($expected, $profile->toHtml());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('John Doe - Profile')
            ->type(new Profile(firstName: 'John', lastName: 'Doe'));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="John Doe - Profile">', $html);
        self::assertStringContainsString('<meta property="og:type" content="profile">', $html);
        self::assertStringContainsString('<meta property="profile:first_name" content="John">', $html);
        self::assertStringContainsString('<meta property="profile:last_name" content="Doe">', $html);
    }
}
