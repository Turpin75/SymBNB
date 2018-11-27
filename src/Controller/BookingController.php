<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="create_booking")
     * @Security("is_granted('ROLE_USER')")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);
            
            // Si les dates ne sont pas disponibles, messsage d'erreur
            if(!$booking->isBookableDates())
            {
                $this->addFlash('warning', "Dates choisies non disponibles !");
            }
            else
            {
                $manager->persist($booking);
                $manager->flush();

                $this->addFlash('success', "Votre réservation a bien été prises en compte !");

                return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
            }
        }
        
        return $this->render('booking/book.html.twig', 
        [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher une réservation
     * 
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking)
    {
        return $this->render("booking/show.html.twig", ['booking' => $booking]);
    }
}
