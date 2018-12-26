<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Entity\ChangePassword;
use App\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * Permet de modifier le profil de l'utilisateur connecté
     * 
     * @Route("/users/edit-user", name="app_edit_user")
     * @Security("is_granted('ROLE_USER')")
     *
     * @return Response
     */
    public function editUser(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", "Les modifications ont bien été prises en compte.");
        }

        return $this->render("users/edit_user.html.twig", ["form" => $form->createView()]);
    }

     /**
     * Permet de modifier le mot de passe de l'utilisateur connecté
     * 
     * @Route("/users/edit-password", name="app_edit_password")
     * @Security("is_granted('ROLE_USER')")
     *
     * @return Response
     */
    public function changePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        
        $changePassword = new ChangePassword();

        $form = $this->createForm(ChangePasswordType::class, $changePassword);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $newPassword = $changePassword->getNewPassword();
            $password = $encoder->encodePassword($user, $newPassword);

            $user->setPassword($password);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Votre mot de passe a bien été modfifié !");

            return $this->redirectToRoute("homepage");
        }
        
        return $this->render("users/edit_password.html.twig", ['form' => $form->createView()]);
    }

    /**
     * Permet d'afficher la liste des réservations faites par l'utilisateur
     * 
     * @Route("/users/bookings", name="user_bookings")
     * 
     * @Security("is_granted('ROLE_USER')", statusCode=403)
     *
     * @return Response
     */
    public function bookings()
    {
        return $this->render('users/bookings.html.twig');
    }

    /**
     * Permet d'affciher le profil d'un utilisateur (connecté ou pas)
     * 
     * @Route("/users/{pseudo}", name="user_show")
     */
    public function index(User $user)
    {
        return $this->render('users/index.html.twig', ['user' => $user]);
    }

    


}
