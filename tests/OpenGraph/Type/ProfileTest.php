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
    public function it_returns_empty_properties_by_default(): void
    {
        $profile = new Profile();

        self::assertSame([], $profile->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_first_name(): void
    {
        $profile = new Profile(firstName: 'John');

        self::assertSame(['profile:first_name' => 'John'], $profile->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_last_name(): void
    {
        $profile = new Profile(lastName: 'Doe');

        self::assertSame(['profile:last_name' => 'Doe'], $profile->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_username(): void
    {
        $profile = new Profile(username: 'johndoe');

        self::assertSame(['profile:username' => 'johndoe'], $profile->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_gender(): void
    {
        $profile = new Profile(gender: 'male');

        self::assertSame(['profile:gender' => 'male'], $profile->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_all_properties(): void
    {
        $profile = new Profile(
            firstName: 'John',
            lastName: 'Doe',
            username: 'johndoe',
            gender: 'male',
        );

        $expected = [
            'profile:first_name' => 'John',
            'profile:last_name' => 'Doe',
            'profile:username' => 'johndoe',
            'profile:gender' => 'male',
        ];

        self::assertSame($expected, $profile->getProperties());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('John Doe - Profile')
            ->type(new Profile(firstName: 'John', lastName: 'Doe'));

        $data = $og->toArray();

        self::assertSame('John Doe - Profile', $data['og:title']);
        self::assertSame('profile', $data['og:type']);
        self::assertSame('John', $data['profile:first_name']);
        self::assertSame('Doe', $data['profile:last_name']);
    }

    /**
     * @test
     */
    public function it_renders_profile_meta_tags(): void
    {
        $og = (new OpenGraph())
            ->title('John Doe')
            ->type(new Profile(firstName: 'John', lastName: 'Doe'));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:type" content="profile">', $html);
        self::assertStringContainsString('<meta property="profile:first_name" content="John">', $html);
        self::assertStringContainsString('<meta property="profile:last_name" content="Doe">', $html);
    }
}
