<?php

namespace App\Controller;

use App\Service\StatsService;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use App\Repository\BookingRepository;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatsService $statsService, Adrepository $adRepo)
    {   
        $stats = $statsService->getStats();
        
        return $this->render('admin/dashboard/index.html.twig', 
        [
           'stats' => $stats,
           'bestAds' => $adRepo->findBestOrWorstAds(5, 'DESC'),
           'worstAds' => $adRepo->findBestOrWorstAds(5, 'ASC')
        ]);
    }
}
