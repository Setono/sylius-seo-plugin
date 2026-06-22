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
final class ElementContentConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('selector', TextType::class, [
                'label' => 'Selector',
                'help' => 'A CSS selector by default, e.g. script#seo-ggg',
            ])
            ->add('selectorType', ChoiceType::class, [
                'label' => 'Selector type',
                'choices' => ['CSS' => 'css', 'XPath' => 'xpath'],
            ])
            ->add('attribute', TextType::class, [
                'label' => 'Attribute',
                'required' => false,
                'help' => 'Test this attribute instead of the element text content',
            ])
            ->add('jsonPath', TextType::class, [
                'label' => 'JSON path',
                'required' => false,
                'help' => 'Decode the element as JSON and extract this path first, e.g. $.offers[0].price',
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
            'empty_data' => [
                'selectorType' => 'css',
                'assertion' => Assertion::EXISTS,
            ],
        ]);
    }
}
