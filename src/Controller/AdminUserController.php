<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AdminUserController extends AbstractController
{
    /**
     * Permet de se connecter à l'administration
     * 
     * @Route("/admin/login", name="admin_user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('admin/security/login.html.twig', 
        [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Permet de se déconnecter de l' administration
     * 
     * @Route("/admin/logout", name="admin_user_logout")
     */
    public function logout()
    {
        // Controller can be blank : it will never be executed!
        // throw new \Exception("Don't forget to active logout in security.yaml");
    }
}
