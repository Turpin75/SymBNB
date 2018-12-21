<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
 
   /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo)
    {
        return $this->render("home.html.twig", 
        [
            'ads' => $adRepo->findBestOrWorstAds(3, 'DESC'),
            'users' => $userRepo->findBestUsers(2)
        ]);
    }
}

?>