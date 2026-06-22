<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Controller\Admin;

use Setono\SyliusSEOPlugin\Checker\CheckRunnerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RunAllChecksAction extends AbstractAdminAction
{
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator,
        private readonly CheckRunnerInterface $checkRunner,
    ) {
        parent::__construct($router, $translator);
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $this->checkRunner->runAll();
        $this->flash($request, 'success', 'setono_sylius_seo.flash.checks_run');

        return $this->redirectBack($request, 'setono_sylius_seo_admin_issue_index');
    }
}
