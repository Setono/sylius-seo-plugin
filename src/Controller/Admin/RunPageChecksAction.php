<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Controller\Admin;

use Setono\SyliusSEOPlugin\Checker\CheckRunnerInterface;
use Setono\SyliusSEOPlugin\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RunPageChecksAction extends AbstractAdminAction
{
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly CheckRunnerInterface $checkRunner,
    ) {
        parent::__construct($router, $translator);
    }

    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $page = $this->pageRepository->findOneById($id);
        if (null === $page) {
            throw new NotFoundHttpException(sprintf('Page with id "%d" not found.', $id));
        }

        $this->checkRunner->run($page);
        $this->flash($request, 'success', 'setono_sylius_seo.flash.checks_run');

        return $this->redirectBack($request, 'setono_sylius_seo_admin_issue_index');
    }
}
