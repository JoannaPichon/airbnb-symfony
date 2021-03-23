<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use App\Form\AnnonceType;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ads/{slug}/book", name="booking_create")
     */
    public function book(EntityManagerInterface $manager, Request $request, Ad $ad, BookingRepository $repo): Response
    {   

        // debut list dates non dispo pour reservation
        //////////////////////////////////////
        //tableau des dates non dispo

        $notAvailableDays = [];
        // recup toutes les resa
        //findBy qui permet de trouver plusieurs enregistrements avec 4 param
        //      -criteres de selection
        //      -ordre (tri)
        //      -limit (nobre enregistrements)
        //      -offset (a partir de où)
        //      exemple findBy(['author'=> 44])
        $repo = $repo->findBy(['ad' => $ad->getId()]);
        foreach ($repo as $resa) {
            $allTimeStamp = range($resa->getStartDate()->getTimestamp(), $resa->getEndDate()->getTimestamp(), 86400);
            $notAvailableDays = array_merge($notAvailableDays, $allTimeStamp );
        }

        // fin liste days not available
        dump($notAvailableDays);
        
        exit;


        $booking = new Booking();

        $form = $this -> createForm(BookingType::class, $booking);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            
            $booker = $this->getUser();
            $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setCreatedAt(new \DateTime());
            $interval = date_diff($booking->getStartDate(), $booking->getEndDate());
            
            $amount = $interval->days * $ad->getPrice();
            
            $booking->setAmount($amount);

            $chooseDays = range($resa->getStartDate()->getTimestamp(), $resa->getEndDate()->getTimestamp(), 86400);
            
            $available = true;
            foreach ($chooseDays as $day) {
                if (array_search($day, $notAvailableDays)) {
                    $available = fals
                }
            }

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                'La réservation a bien été effectuée'
            );

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

            

        return $this->render('booking/book.html.twig', [
            'form'  => $form->createView(),
            'ad'    => $ad
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/bookings/{id}", name="booking_index")
     */
    public function bookings(User $user): Response
    {
        dump($user);

        return $this->render('booking/index.html.twig', [
            'booker'  => $user
        ]);
    }




    /**
     * @IsGranted("ROLE_USER")
     * @Route("/booking/{id}", name="booking_show")
     */
    public function showBook(Booking $booking): Response
    {
        

        
            

        return $this->render('booking/show.html.twig', [
            'booking'   => $booking,
        ]);
    }
}
