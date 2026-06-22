<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber;

use Sylius\Bundle\AdminBundle\Menu\MainMenuBuilder;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminMenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            MainMenuBuilder::EVENT_NAME => 'addMenuItems',
        ];
    }

    public function addMenuItems(MenuBuilderEvent $event): void
    {
        $seo = $event->getMenu()
            ->addChild('setono_sylius_seo')
            ->setLabel('setono_sylius_seo.menu.seo')
            ->setLabelAttribute('icon', 'search')
        ;

        $seo
            ->addChild('setono_sylius_seo_pages', ['route' => 'setono_sylius_seo_admin_page_index'])
            ->setLabel('setono_sylius_seo.menu.pages')
            ->setLabelAttribute('icon', 'file-text')
        ;

        $seo
            ->addChild('setono_sylius_seo_issues', ['route' => 'setono_sylius_seo_admin_issue_index'])
            ->setLabel('setono_sylius_seo.menu.issues')
            ->setLabelAttribute('icon', 'alert-triangle')
        ;
    }
}
