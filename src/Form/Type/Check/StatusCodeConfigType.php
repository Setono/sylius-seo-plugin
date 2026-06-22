<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Form\Type\Check;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array<string, mixed>>
 */
final class StatusCodeConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expected', IntegerType::class, [
                'label' => 'Expected status code',
                'help' => 'e.g. 200, 301 or 404',
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
            'empty_data' => ['expected' => 200],
        ]);
    }
}
