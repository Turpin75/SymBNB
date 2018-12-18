<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\Pagination;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Permet d'afficher les réservations
     * 
     * @Route("/admin/bookings/{page}", name="admin_bookings_index", requirements={"page": "\d+"})
     */
    public function index(BookingRepository $repo, Pagination $pagination, $page = 1)
    {
        $pagination->setEntityClass(Booking::class)
                    ->setCurrentPage($page);
        
        return $this->render('admin/bookings/index.html.twig', 
        [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'éditer une réservation
     *
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash('success', "La réservation n°{$booking->getId()} a bien été modidiée.");

            return $this->redirectToRoute("admin_bookings_index");
        }

        return $this->render('admin/bookings/edit.html.twig', 
        [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une réservation
     *
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     */
    public function delete(Booking $booking, ObjectManager $manager)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash('success', "La réservation de {$booking->getBooker()->getFullName()} a bien été supprimée.");

        return $this->redirectToRoute("admin_bookings_index");
    }
}
