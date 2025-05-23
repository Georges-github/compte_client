<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CustomLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onLogoutSuccess(Request $request): RedirectResponse
    {
        $request->getSession()->getFlashBag()->add('success', 'Vous avez été déconnecté avec succès.');

        return new RedirectResponse($this->router->generate('app_login'));
    }
}
