<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusSEOPlugin\Model\IssueInterface;
use Setono\SyliusSEOPlugin\Model\IssueStatus;
use Setono\SyliusSEOPlugin\Repository\IssueRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class IgnoreIssueAction extends AbstractAdminAction
{
    /**
     * @param IssueRepositoryInterface<IssueInterface> $issueRepository
     */
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator,
        private readonly IssueRepositoryInterface $issueRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($router, $translator);
    }

    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $issue = $this->issueRepository->find($id);
        if (null === $issue) {
            throw new NotFoundHttpException(sprintf('Issue with id "%d" not found.', $id));
        }

        $issue->setStatus(IssueStatus::Ignored);
        $this->entityManager->flush();
        $this->flash($request, 'success', 'setono_sylius_seo.flash.issue_ignored');

        return $this->redirectBack($request, 'setono_sylius_seo_admin_issue_index');
    }
}
