<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Form\Type\Check;

use Setono\SyliusSEOPlugin\Checker\Detector\Builtin\Assertion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array<string, mixed>>
 */
final class HeaderConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Header name',
                'help' => 'e.g. Content-Type or Strict-Transport-Security',
            ])
            ->add('assertion', ChoiceType::class, [
                'label' => 'Assertion',
                'choices' => CheckConfigChoices::assertions(),
            ])
            ->add('value', TextType::class, [
                'label' => 'Value',
                'required' => false,
            ])
            ->add('severity', ChoiceType::class, [
                'label' => 'Severity',
                'required' => false,
                'placeholder' => 'Default',
                'choices' => CheckConfigChoices::severities(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'empty_data' => ['assertion' => Assertion::EXISTS],
        ]);
    }
}
