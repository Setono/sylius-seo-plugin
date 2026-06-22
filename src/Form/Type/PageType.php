<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Form\Type;

use Setono\SyliusSEOPlugin\Checker\UrlResolver\CompositeUrlResolver;
use Setono\SyliusSEOPlugin\Model\Page;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Locales;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

/**
 * @extends AbstractType<Page>
 */
final class PageType extends AbstractType
{
    public function __construct(
        private readonly CompositeUrlResolver $urlResolver,
        private readonly LocaleProviderInterface $localeProvider,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.ui.name',
            ])
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'sylius.ui.channel',
            ])
            ->add('localeCode', ChoiceType::class, [
                'label' => 'sylius.ui.locale',
                'required' => false,
                'placeholder' => 'setono_sylius_seo.ui.use_channel_default',
                'choices' => $this->localeChoices(),
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->typeChoices(),
                'placeholder' => '—',
            ])
            ->add('routeName', TextType::class, [
                'label' => 'Route name',
                'required' => false,
                'help' => 'Only used for the "custom route" page type',
            ])
            ->add('sampleResourceId', TextType::class, [
                'label' => 'Sample resource',
                'required' => false,
                'help' => 'A product/taxon code for dynamic page types; leave empty to auto-pick one',
            ])
            ->add('checks', LiveCollectionType::class, [
                'label' => 'setono_sylius_seo.ui.checks',
                'entry_type' => CheckAssignmentType::class,
                'button_add_options' => ['label' => 'setono_sylius_seo.ui.add_check'],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.ui.enabled',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Page::class]);
    }

    /**
     * @return array<string, string>
     */
    private function typeChoices(): array
    {
        $choices = [];
        foreach ($this->urlResolver->getTypes() as $type) {
            $choices[ucfirst(str_replace('_', ' ', $type))] = $type;
        }

        return $choices;
    }

    /**
     * The store's enabled locales, mapped as "display name" => "code".
     *
     * @return array<string, string>
     */
    private function localeChoices(): array
    {
        $choices = [];
        foreach ($this->localeProvider->getAvailableLocalesCodes() as $code) {
            $choices[Locales::getName($code)] = $code;
        }

        return $choices;
    }
}
