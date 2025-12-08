<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph;

use Setono\SyliusSEOPlugin\OpenGraph\Property\Audio;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Image;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Video;
use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Website;

/**
 * @see https://ogp.me/
 */
final class OpenGraph
{
    private ?string $title = null;

    private TypeInterface $type;

    private ?string $url = null;

    private ?string $description = null;

    private ?string $determiner = null;

    private ?string $locale = null;

    /** @var list<string> */
    private array $localeAlternates = [];

    private ?string $siteName = null;

    /** @var list<Image> */
    private array $images = [];

    /** @var list<Video> */
    private array $videos = [];

    /** @var list<Audio> */
    private array $audios = [];

    public function __construct()
    {
        $this->type = new Website();
    }

    public function title(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function type(?TypeInterface $type): self
    {
        if (null !== $type) {
            $this->type = $type;
        }

        return $this;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    public function url(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the word that appears before this object's title in a sentence.
     * Should be one of: "a", "an", "the", "", "auto".
     */
    public function determiner(?string $determiner): self
    {
        $this->determiner = $determiner;

        return $this;
    }

    public function getDeterminer(): ?string
    {
        return $this->determiner;
    }

    /**
     * Set the locale these tags are marked up in.
     * Format: language_TERRITORY (e.g., "en_US", "fr_FR").
     */
    public function locale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function localeAlternate(string $locale): self
    {
        $this->localeAlternates[] = $locale;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getLocaleAlternates(): array
    {
        return $this->localeAlternates;
    }

    public function siteName(?string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    /**
     * Add an image to represent your object.
     *
     * @param Image|string $image Image object or URL
     */
    public function image(Image|string $image): self
    {
        if (is_string($image)) {
            $image = new Image($image);
        }

        $this->images[] = $image;

        return $this;
    }

    /**
     * @return list<Image>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Add a video to complement your object.
     *
     * @param Video|string $video Video object or URL
     */
    public function video(Video|string $video): self
    {
        if (is_string($video)) {
            $video = new Video($video);
        }

        $this->videos[] = $video;

        return $this;
    }

    /**
     * @return list<Video>
     */
    public function getVideos(): array
    {
        return $this->videos;
    }

    /**
     * Add an audio file to complement your object.
     *
     * @param Audio|string $audio Audio object or URL
     */
    public function audio(Audio|string $audio): self
    {
        if (is_string($audio)) {
            $audio = new Audio($audio);
        }

        $this->audios[] = $audio;

        return $this;
    }

    /**
     * @return list<Audio>
     */
    public function getAudios(): array
    {
        return $this->audios;
    }

    /**
     * Render the Open Graph data as HTML meta tags.
     */
    public function toHtml(): string
    {
        $html = [];

        if (null !== $this->title) {
            $html[] = self::renderMetaTag('og:title', $this->title);
        }

        $html[] = self::renderMetaTag('og:type', $this->type->getType());

        if (null !== $this->url) {
            $html[] = self::renderMetaTag('og:url', $this->url);
        }

        if (null !== $this->description) {
            $html[] = self::renderMetaTag('og:description', $this->description);
        }

        if (null !== $this->determiner) {
            $html[] = self::renderMetaTag('og:determiner', $this->determiner);
        }

        if (null !== $this->locale) {
            $html[] = self::renderMetaTag('og:locale', $this->locale);
        }

        foreach ($this->localeAlternates as $localeAlternate) {
            $html[] = self::renderMetaTag('og:locale:alternate', $localeAlternate);
        }

        if (null !== $this->siteName) {
            $html[] = self::renderMetaTag('og:site_name', $this->siteName);
        }

        foreach ($this->images as $image) {
            $html[] = $image->toHtml();
        }

        foreach ($this->videos as $video) {
            $html[] = $video->toHtml();
        }

        foreach ($this->audios as $audio) {
            $html[] = $audio->toHtml();
        }

        $typeHtml = $this->type->toHtml();
        if ('' !== $typeHtml) {
            $html[] = $typeHtml;
        }

        return implode("\n", $html);
    }

    public static function renderMetaTag(string $property, bool|float|int|string|null $content): ?string
    {
        if (null === $content) {
            return null;
        }

        return sprintf(
            '<meta property="%s" content="%s">',
            $property,
            htmlspecialchars((string) $content, \ENT_QUOTES | \ENT_HTML5, 'UTF-8'),
        );
    }
}
