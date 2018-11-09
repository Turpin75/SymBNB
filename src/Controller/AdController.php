<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;



class AdController extends AbstractController
{
    /**
     * Liste toutes les annonces de la base de données
     * 
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        
        return $this->render('ad/index.html.twig', ['ads' => $ads]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     * @Security("is_granted('ROLE_USER')")
     *
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $ad = new Ad();

        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $ad->setAuthor($this->getUser());
            
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success' , 
                    'L\'annonce <strong>' . $ad->getTitle() . '</<strong> a bien été enregistrée !');

           return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }
        
        return $this->render("ad/new.html.twig", ['form' => $form->createView()]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous ne pouvez modifier cette annonce !")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success' , 
                    'Les modifications de l\'annonce <strong>' . $ad->getTitle() . 
                    '</strong> ont bien été enregistrées !');

           return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }
        
        return $this->render('ad/edit.html.twig', ['form' => $form->createView(), 'ad' => $ad]);
    }

    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous ne pouvez supprimer cette annonce !")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        $user = $this->getUser();

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success', "L'annonce : <strong>".$ad->getTitle()."</strong> a bien été supprimée");

        return $this->redirectToRoute("user_show", ['pseudo' => $user->getPseudo()]);
    }

    /**
     * Permet d'afficher une seule annonce
     * 
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad)
    {
        $user = $ad->getAuthor();

        return $this->render("ad/show.html.twig", ['ad' => $ad, 'user' => $user]);
    }

}
