<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Service\Pagination;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AdminUserController extends AbstractController
{
    /**
     * Affiche un tableau des utilisateurs au niveau de l'administration
     *
     * @Route("/admin/users/{page}", name="admin_users_index", requirements={"page": "\d+"})
     */
    public function index(UserRepository $userRepo, Pagination $pagination, $page = 1)
    {
        $pagination->setEntityClass(User::class)
                    ->setCurrentPage($page);
        
        return $this->render('admin/users/index.html.twig', 
        [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition d'un utilisateur
     * 
     * @Route("admin/users/{id}/edit", name="admin_users_edit")
     */
    public function edit(User $user, ObjectManager $manager, Request $request)
    {
        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", "Les modifications ont bien été prises en compte.");

            return $this->redirectToRoute("admin_users_index");
        }
        
        return $this->render("admin/users/edit.html.twig", 
        [
            'form' => $form->createView(), 
            'user' => $user
        ]);
    }

    /**
     * Permet de supprimer un utlilisateur
     * 
     * @Route("admin/users/{id}/delete", name="admin_users_delete")
     */
    public function delete(User $user, ObjectManager $manager, Request $request)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash("success", "L'utilisateur <stronge>: ".$user->getPseudo()."</strong> a bien été supprimé.");

        return $this->redirectToRoute("admin_users_index");
    }
    
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
