<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Form\Type\Check;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array<string, mixed>>
 */
final class ElementExistsConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('selector', TextType::class, [
                'label' => 'Selector',
                'help' => 'A CSS selector by default, e.g. link[rel="canonical"]',
            ])
            ->add('selectorType', ChoiceType::class, [
                'label' => 'Selector type',
                'choices' => ['CSS' => 'css', 'XPath' => 'xpath'],
            ])
            ->add('min', IntegerType::class, [
                'label' => 'Minimum count',
                'required' => false,
                'help' => 'Defaults to 1 (the element must exist)',
            ])
            ->add('max', IntegerType::class, [
                'label' => 'Maximum count',
                'required' => false,
                'help' => 'Leave empty for no upper bound',
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
            'empty_data' => ['selectorType' => 'css'],
        ]);
    }
}
