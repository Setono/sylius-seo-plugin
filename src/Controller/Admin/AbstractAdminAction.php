<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAdminAction
{
    public function __construct(
        protected readonly RouterInterface $router,
        protected readonly TranslatorInterface $translator,
    ) {
    }

    protected function flash(Request $request, string $type, string $messageKey): void
    {
        $session = $request->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add($type, $this->translator->trans($messageKey));
        }
    }

    protected function redirectBack(Request $request, string $fallbackRoute): RedirectResponse
    {
        $referer = $request->headers->get('referer');
        if (\is_string($referer) && '' !== $referer) {
            return new RedirectResponse($referer);
        }

        return new RedirectResponse($this->router->generate($fallbackRoute));
    }
}
