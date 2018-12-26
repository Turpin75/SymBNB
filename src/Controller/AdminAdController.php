<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Affiche un tableau des annonces au niveau de l'administration
     * 
     * @Route("/admin/ads/{page}", name="admin_ads_index", requirements={"page": "\d+"})
     */
    public function index(AdRepository $repo, Pagination $pagination, $page = 1)
    {
        $pagination->setEntityClass(Ad::class)
                    ->setCurrentPage($page);
        
        return $this->render('admin/ads/index.html.twig', 
        [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition d'une annonce
     * 
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
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

            $this->addFlash('success', "L'annonce : <strong>".$ad->getTitle()."</strong> a bien été modifiée !");

            return $this->redirectToRoute("admin_ads_index");
        }

        return $this->render('admin/ads/edit.html.twig', 
        [
            'ad' => $ad,
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success', "L'annonce :<strong>".$ad->getTitle()."</strong> a bien été supprimée");

        return $this->redirectToRoute('admin_ads_index');
    }
}
