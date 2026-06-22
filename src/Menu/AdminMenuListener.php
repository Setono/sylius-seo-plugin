<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $seo = $menu
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
