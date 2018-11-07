<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Entity\ChangePassword;
use App\Form\RegistrationType;
use App\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * Permet de se connecter
     * 
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', 
        [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // Controller can be blank : it will never be executed!
        // throw new \Exception("Don't forget to active logout in security.yaml");
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", 
                    "Votre compte a bien été créé. Vous pouvez mainteant vous connecter !");
            
            return $this->redirectToRoute("app_login");
        }

        return $this->render("security/registration.html.twig", 
        [
            'form' => $form->createView()
        ]);
    }

}
