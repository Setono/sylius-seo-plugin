<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Form\Type;

use Setono\SyliusSEOPlugin\Checker\Detector\ConfigurableIssueDetectorInterface;
use Setono\SyliusSEOPlugin\Checker\Detector\DetectorRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * One check assigned to a page: a check code plus, for configurable checks, a configuration
 * sub-form. The config sub-form is swapped in based on the chosen code via form events. A stable
 * id is generated on first save so the resulting issue's fingerprint stays stable across runs.
 *
 * @extends AbstractType<array<string, mixed>>
 */
final class CheckAssignmentType extends AbstractType
{
    public function __construct(private readonly DetectorRegistryInterface $detectorRegistry)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, ['required' => false])
            ->add('code', ChoiceType::class, [
                'label' => 'setono_sylius_seo.ui.check',
                'choices' => $this->codeChoices(),
                'placeholder' => '—',
            ])
        ;

        $registry = $this->detectorRegistry;

        $addConfig = static function (FormInterface $form, mixed $code) use ($registry): void {
            if (!\is_string($code) || '' === $code) {
                return;
            }

            $detector = $registry->get($code);
            if ($detector instanceof ConfigurableIssueDetectorInterface) {
                $form->add('config', $detector->getConfigFormType(), ['label' => false, 'required' => false]);
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($addConfig): void {
            $data = $event->getData();
            $addConfig($event->getForm(), \is_array($data) ? ($data['code'] ?? null) : null);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event) use ($addConfig): void {
            $data = $event->getData();
            $addConfig($event->getForm(), \is_array($data) ? ($data['code'] ?? null) : null);

            if (\is_array($data) && ('' === ($data['id'] ?? '') || !isset($data['id']))) {
                $data['id'] = bin2hex(random_bytes(8));
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'allow_extra_fields' => true,
            'empty_data' => [],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function codeChoices(): array
    {
        $choices = [];
        foreach (array_keys($this->detectorRegistry->all()) as $code) {
            $choices['setono_sylius_seo.check.' . $code . '.label'] = $code;
        }

        ksort($choices);

        return $choices;
    }
}
