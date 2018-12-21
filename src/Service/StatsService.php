<?php

namespace App\Service;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use App\Repository\BookingRepository;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;

class StatsService
{
    private $manager;
    private $adRepo;
    private $userRepo;
    private $commentRepo;
    private $bookingRepo;

    public function __construct(ObjectManager $manager, AdRepository $adRepo, UserRepository $userRepo, CommentRepository $commentRepo, BookingRepository $bookingRepo)
    {
        $this->manager = $manager;
        $this->adRepo = $adRepo;
        $this->userRepo = $userRepo;
        $this->commentRepo = $commentRepo;
        $this->bookingRepo = $bookingRepo;
    }

    /**
     * Retourne le nombre total d'utilisateurs
     */
    public function getUsersCount()
    {
        return count($this->userRepo->findAll());
    }

    /**
     * Retourne le nombre total d'annonces
     */
    public function getAdsCount()
    {
        return count($this->adRepo->findAll());
    }

    
    /**
     * Retourne le nombre total de réservations
     */
    public function getBookingsCount()
    {
        return count($this->bookingRepo->findAll());
    }
    
    /**
     * Retourne le nombre total de commentaires
     */
    public function getCommentsCount()
    {
        return count($this->commentRepo->findAll());
    }

    /**
     * Retourne un tableau de statistiques :
     * nombre d'utlilisateurs, d'annonces, de commentaires, de réservations
     *
     * @return void
     */
    public function getStats()
    {
        $ads = $this->getAdsCount();
        $users = $this->getUsersCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();

        return compact('ads', 'users', 'bookings', 'comments');
    }
    
}