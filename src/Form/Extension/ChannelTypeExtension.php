<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('robotsTxt', TextareaType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'setono_sylius_seo.form.channel.robots_txt_placeholder',
            ],
            'required' => false,
        ]);
    }

    public static function getExtendedTypes(): \Generator
    {
        yield ChannelType::class;
    }
}
