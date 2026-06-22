<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Form\Type;

use PHPUnit\Framework\Attributes\Test;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\ElementContentDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\TitleLengthDetector;
use Setono\SyliusSEOPlugin\Checker\Detector\DetectorRegistry;
use Setono\SyliusSEOPlugin\Form\Type\Check\ElementContentConfigType;
use Setono\SyliusSEOPlugin\Form\Type\CheckAssignmentType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class CheckAssignmentTypeTest extends TypeTestCase
{
    #[Test]
    public function it_generates_an_id_for_a_zero_config_check(): void
    {
        $form = $this->factory->create(CheckAssignmentType::class);
        $form->submit(['code' => 'title_length']);

        self::assertTrue($form->isSynchronized());

        /** @var array<string, mixed> $data */
        $data = $form->getData();
        self::assertSame('title_length', $data['code']);
        self::assertArrayHasKey('id', $data);
        self::assertNotSame('', $data['id']);
    }

    #[Test]
    public function it_builds_the_config_sub_form_for_a_configurable_check(): void
    {
        $form = $this->factory->create(CheckAssignmentType::class);
        $form->submit(['code' => 'element_content', 'config' => ['selector' => 'h1', 'assertion' => 'exists']]);

        self::assertTrue($form->isSynchronized());

        /** @var array{code: string, config: array<string, mixed>} $data */
        $data = $form->getData();
        self::assertSame('element_content', $data['code']);
        self::assertSame('h1', $data['config']['selector']);
    }

    protected function getExtensions(): array
    {
        $registry = new DetectorRegistry([new TitleLengthDetector(), new ElementContentDetector()]);

        return [
            new PreloadedExtension([
                new CheckAssignmentType($registry),
                new ElementContentConfigType(),
            ], []),
        ];
    }
}
