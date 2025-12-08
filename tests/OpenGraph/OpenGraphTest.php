<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Audio;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Image;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Video;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Profile;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Website;

final class OpenGraphTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_empty_open_graph(): void
    {
        $og = new OpenGraph();

        self::assertNull($og->getTitle());
        self::assertNull($og->getUrl());
        self::assertNull($og->getDescription());
        self::assertNull($og->getDeterminer());
        self::assertNull($og->getLocale());
        self::assertSame([], $og->getLocaleAlternates());
        self::assertNull($og->getSiteName());
        self::assertSame([], $og->getImages());
        self::assertSame([], $og->getVideos());
        self::assertSame([], $og->getAudios());
    }

    /**
     * @test
     */
    public function it_sets_title(): void
    {
        $og = (new OpenGraph())->title('My Page Title');

        self::assertSame('My Page Title', $og->getTitle());
    }

    /**
     * @test
     */
    public function it_sets_type(): void
    {
        $type = new Website();
        $og = (new OpenGraph())->type($type);

        self::assertSame($type, $og->getType());
    }

    /**
     * @test
     */
    public function it_sets_url(): void
    {
        $og = (new OpenGraph())->url('https://example.com');

        self::assertSame('https://example.com', $og->getUrl());
    }

    /**
     * @test
     */
    public function it_sets_description(): void
    {
        $og = (new OpenGraph())->description('A brief description of my page.');

        self::assertSame('A brief description of my page.', $og->getDescription());
    }

    /**
     * @test
     */
    public function it_sets_determiner(): void
    {
        $og = (new OpenGraph())->determiner('the');

        self::assertSame('the', $og->getDeterminer());
    }

    /**
     * @test
     */
    public function it_sets_locale(): void
    {
        $og = (new OpenGraph())->locale('en_US');

        self::assertSame('en_US', $og->getLocale());
    }

    /**
     * @test
     */
    public function it_adds_locale_alternates(): void
    {
        $og = (new OpenGraph())
            ->localeAlternate('fr_FR')
            ->localeAlternate('de_DE');

        self::assertSame(['fr_FR', 'de_DE'], $og->getLocaleAlternates());
    }

    /**
     * @test
     */
    public function it_sets_site_name(): void
    {
        $og = (new OpenGraph())->siteName('My Website');

        self::assertSame('My Website', $og->getSiteName());
    }

    /**
     * @test
     */
    public function it_adds_image_from_url(): void
    {
        $og = (new OpenGraph())->image('https://example.com/image.jpg');

        $images = $og->getImages();
        self::assertCount(1, $images);
        self::assertSame('https://example.com/image.jpg', $images[0]->getUrl());
    }

    /**
     * @test
     */
    public function it_adds_image_from_object(): void
    {
        $image = (new Image('https://example.com/image.jpg'))
            ->dimensions(1200, 630);

        $og = (new OpenGraph())->image($image);

        $images = $og->getImages();
        self::assertCount(1, $images);
        self::assertSame($image, $images[0]);
    }

    /**
     * @test
     */
    public function it_adds_multiple_images(): void
    {
        $og = (new OpenGraph())
            ->image('https://example.com/image1.jpg')
            ->image('https://example.com/image2.jpg');

        self::assertCount(2, $og->getImages());
    }

    /**
     * @test
     */
    public function it_adds_video_from_url(): void
    {
        $og = (new OpenGraph())->video('https://example.com/video.mp4');

        $videos = $og->getVideos();
        self::assertCount(1, $videos);
        self::assertSame('https://example.com/video.mp4', $videos[0]->getUrl());
    }

    /**
     * @test
     */
    public function it_adds_video_from_object(): void
    {
        $video = (new Video('https://example.com/video.mp4'))
            ->dimensions(1920, 1080);

        $og = (new OpenGraph())->video($video);

        $videos = $og->getVideos();
        self::assertCount(1, $videos);
        self::assertSame($video, $videos[0]);
    }

    /**
     * @test
     */
    public function it_adds_audio_from_url(): void
    {
        $og = (new OpenGraph())->audio('https://example.com/audio.mp3');

        $audios = $og->getAudios();
        self::assertCount(1, $audios);
        self::assertSame('https://example.com/audio.mp3', $audios[0]->getUrl());
    }

    /**
     * @test
     */
    public function it_adds_audio_from_object(): void
    {
        $audio = (new Audio('https://example.com/audio.mp3'))
            ->type('audio/mpeg');

        $og = (new OpenGraph())->audio($audio);

        $audios = $og->getAudios();
        self::assertCount(1, $audios);
        self::assertSame($audio, $audios[0]);
    }

    /**
     * @test
     */
    public function it_supports_fluent_interface(): void
    {
        $og = (new OpenGraph())
            ->title('Page Title')
            ->type(new Website())
            ->url('https://example.com')
            ->description('Page description')
            ->locale('en_US')
            ->siteName('My Site')
            ->image('https://example.com/image.jpg');

        self::assertSame('Page Title', $og->getTitle());
        self::assertSame('website', $og->getType()->getType());
        self::assertSame('https://example.com', $og->getUrl());
        self::assertSame('Page description', $og->getDescription());
        self::assertSame('en_US', $og->getLocale());
        self::assertSame('My Site', $og->getSiteName());
        self::assertCount(1, $og->getImages());
    }

    /**
     * @test
     */
    public function it_converts_basic_data_to_array(): void
    {
        $og = (new OpenGraph())
            ->title('Page Title')
            ->type(new Website())
            ->url('https://example.com')
            ->description('Page description');

        $expected = [
            'og:title' => 'Page Title',
            'og:type' => 'website',
            'og:url' => 'https://example.com',
            'og:description' => 'Page description',
        ];

        self::assertSame($expected, $og->toArray());
    }

    /**
     * @test
     */
    public function it_converts_all_data_to_array(): void
    {
        $og = (new OpenGraph())
            ->title('Page Title')
            ->type(new Website())
            ->url('https://example.com')
            ->description('Page description')
            ->determiner('the')
            ->locale('en_US')
            ->localeAlternate('fr_FR')
            ->siteName('My Site')
            ->image('https://example.com/image.jpg');

        $data = $og->toArray();

        self::assertSame('Page Title', $data['og:title']);
        self::assertSame('website', $data['og:type']);
        self::assertSame('https://example.com', $data['og:url']);
        self::assertSame('Page description', $data['og:description']);
        self::assertSame('the', $data['og:determiner']);
        self::assertSame('en_US', $data['og:locale']);
        self::assertSame(['fr_FR'], $data['og:locale:alternate']);
        self::assertSame('My Site', $data['og:site_name']);
        self::assertSame('https://example.com/image.jpg', $data['og:image']);
    }

    /**
     * @test
     */
    public function it_includes_type_specific_properties(): void
    {
        $og = (new OpenGraph())
            ->title('John Doe')
            ->type(new Profile(firstName: 'John', lastName: 'Doe'));

        $data = $og->toArray();

        self::assertSame('John Doe', $data['og:title']);
        self::assertSame('profile', $data['og:type']);
        self::assertSame('John', $data['profile:first_name']);
        self::assertSame('Doe', $data['profile:last_name']);
    }

    /**
     * @test
     */
    public function it_handles_multiple_images_in_array(): void
    {
        $og = (new OpenGraph())
            ->image((new Image('https://example.com/image1.jpg'))->width(100))
            ->image((new Image('https://example.com/image2.jpg'))->width(200));

        $data = $og->toArray();

        self::assertSame(['https://example.com/image1.jpg', 'https://example.com/image2.jpg'], $data['og:image']);
        self::assertSame([100, 200], $data['og:image:width']);
    }

    /**
     * @test
     */
    public function it_renders_default_type_when_no_other_data(): void
    {
        $og = new OpenGraph();

        self::assertSame('<meta property="og:type" content="website">', $og->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_basic_html_meta_tags(): void
    {
        $og = (new OpenGraph())
            ->title('Page Title')
            ->type(new Website());

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="Page Title">', $html);
        self::assertStringContainsString('<meta property="og:type" content="website">', $html);
    }

    /**
     * @test
     */
    public function it_escapes_html_special_characters_in_content(): void
    {
        $og = (new OpenGraph())
            ->title('Title with "quotes" & <special> chars');

        $html = $og->toHtml();

        self::assertStringContainsString('content="Title with &quot;quotes&quot; &amp; &lt;special&gt; chars"', $html);
    }

    /**
     * @test
     */
    public function it_renders_multiple_locale_alternates(): void
    {
        $og = (new OpenGraph())
            ->localeAlternate('fr_FR')
            ->localeAlternate('de_DE');

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:locale:alternate" content="fr_FR">', $html);
        self::assertStringContainsString('<meta property="og:locale:alternate" content="de_DE">', $html);
    }

    /**
     * @test
     */
    public function it_renders_image_with_structured_properties(): void
    {
        $og = (new OpenGraph())
            ->image(
                (new Image('https://example.com/image.jpg'))
                    ->dimensions(1200, 630)
                    ->alt('Image description'),
            );

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:image" content="https://example.com/image.jpg">', $html);
        self::assertStringContainsString('<meta property="og:image:width" content="1200">', $html);
        self::assertStringContainsString('<meta property="og:image:height" content="630">', $html);
        self::assertStringContainsString('<meta property="og:image:alt" content="Image description">', $html);
    }
}
