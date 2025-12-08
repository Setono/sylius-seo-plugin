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
     * Convert the Open Graph data to an array of property => value pairs.
     *
     * @return array<string, scalar|list<scalar>>
     */
    public function toArray(): array
    {
        $data = array_filter([
            'og:title' => $this->title,
            'og:type' => $this->type->getType(),
            'og:url' => $this->url,
            'og:description' => $this->description,
            'og:determiner' => $this->determiner,
            'og:locale' => $this->locale,
            'og:locale:alternate' => [] !== $this->localeAlternates ? $this->localeAlternates : null,
            'og:site_name' => $this->siteName,
        ], static fn ($value) => null !== $value);

        foreach ($this->images as $image) {
            foreach ($image->toArray() as $property => $value) {
                $data[$property] = array_key_exists($property, $data)
                    ? $this->mergeArrayValue($data[$property], $value)
                    : $value;
            }
        }

        foreach ($this->videos as $video) {
            foreach ($video->toArray() as $property => $value) {
                $data[$property] = array_key_exists($property, $data)
                    ? $this->mergeArrayValue($data[$property], $value)
                    : $value;
            }
        }

        foreach ($this->audios as $audio) {
            foreach ($audio->toArray() as $property => $value) {
                $data[$property] = array_key_exists($property, $data)
                    ? $this->mergeArrayValue($data[$property], $value)
                    : $value;
            }
        }

        return array_merge($data, $this->type->getProperties());
    }

    /**
     * @param scalar|list<scalar> $existing
     *
     * @return list<scalar>
     */
    private function mergeArrayValue(array|bool|float|int|string $existing, bool|float|int|string $value): array
    {
        if (is_array($existing)) {
            $existing[] = $value;

            return $existing;
        }

        return [$existing, $value];
    }

    /**
     * Render the Open Graph data as HTML meta tags.
     */
    public function toHtml(): string
    {
        $html = [];

        foreach ($this->toArray() as $property => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $html[] = $this->renderMetaTag($property, $item);
                }
            } else {
                $html[] = $this->renderMetaTag($property, $value);
            }
        }

        return implode("\n", $html);
    }

    private function renderMetaTag(string $property, bool|float|int|string $content): string
    {
        return sprintf(
            '<meta property="%s" content="%s">',
            htmlspecialchars($property, \ENT_QUOTES | \ENT_HTML5, 'UTF-8'),
            htmlspecialchars((string) $content, \ENT_QUOTES | \ENT_HTML5, 'UTF-8'),
        );
    }
}
