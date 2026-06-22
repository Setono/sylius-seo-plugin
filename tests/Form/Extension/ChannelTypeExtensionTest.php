<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Form\Extension;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\Form\Extension\ChannelTypeExtension;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryBuilder;

final class ChannelTypeExtensionTest extends TestCase
{
    #[Test]
    public function it_extends_channel_type(): void
    {
        $extendedTypes = ChannelTypeExtension::getExtendedTypes();

        self::assertContains(ChannelType::class, $extendedTypes);
    }

    #[Test]
    public function it_adds_robots_txt_field(): void
    {
        $extension = new ChannelTypeExtension();

        $formFactory = (new FormFactoryBuilder())->getFormFactory();
        $builder = $formFactory->createBuilder();

        $extension->buildForm($builder, []);

        $form = $builder->getForm();

        self::assertTrue($form->has('robotsTxt'));
    }

    #[Test]
    public function it_configures_robots_txt_field_correctly(): void
    {
        $extension = new ChannelTypeExtension();

        $formFactory = (new FormFactoryBuilder())->getFormFactory();
        $builder = $formFactory->createBuilder();

        $extension->buildForm($builder, []);

        $form = $builder->getForm();
        $robotsTxtConfig = $form->get('robotsTxt')->getConfig();

        self::assertFalse($robotsTxtConfig->getRequired());
        self::assertSame(TextareaType::class, $robotsTxtConfig->getType()->getInnerType()::class);
    }

    #[Test]
    public function it_submits_robots_txt_data(): void
    {
        $extension = new ChannelTypeExtension();

        $formFactory = (new FormFactoryBuilder())->getFormFactory();
        $builder = $formFactory->createBuilder(FormType::class, ['robotsTxt' => null]);

        $extension->buildForm($builder, []);

        $form = $builder->getForm();
        $form->submit(['robotsTxt' => "User-agent: *\nDisallow: /admin"]);

        self::assertTrue($form->isValid());
        self::assertSame("User-agent: *\nDisallow: /admin", $form->get('robotsTxt')->getData());
    }
}
