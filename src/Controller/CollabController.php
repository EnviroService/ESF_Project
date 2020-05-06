<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Tracking;
use App\Repository\BookingRepository;
use App\Repository\SolutionRepository;
use App\Repository\TrackingRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @IsGranted("ROLE_COLLABORATOR")
 */

class CollabController extends AbstractController
{
    /**
     * @Route("repair/received/booking/{id}", name="received_booking", methods={"GET"})
     * @param Booking $booking
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function isReceived(Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $tracking = $booking->getTrackings();
        $booking->setIsReceived(true)
                ->setReceivedDate(new DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($booking);
        $entityManager->flush();

        return $this->redirectToRoute('booking_show', [
            'id' => $booking->getId(),
        ]);
    }

    /**
     * @Route("returned/tracking/{id}", name="returned_tracking", methods={"GET"})
     * @param Tracking $tracking
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function isReturned(Tracking $tracking, EntityManagerInterface $entityManager): Response
    {
        $tracks = $tracking->getBooking();
        $tracking->setIsReturned(true)
            ->setReturnedDate(new DateTime('now'));

        $entityManager->persist($tracking);
        $entityManager->flush();

        return $this->redirectToRoute('tracking_show', [
            'id' => $tracking->getId(),
        ]);
    }


// in collab dashboard
    /**
     * @Route("/tracking/returned", name="tracking_is_returned")
     * @param TrackingRepository $trackingRepository
     * @param BookingRepository $bookingRepository
     * @param SolutionRepository $solutionRepository
     * @return Response
     */
    public function phoneIsReturned(TrackingRepository $trackingRepository,
                                    BookingRepository $bookingRepository,
                                    SolutionRepository $solutionRepository ): Response
    {
        $tracking = $trackingRepository->findBy(['isSent'=>true]);
        $trackings = $trackingRepository->findBy(['isReturned'=>true]);
        $booking = $bookingRepository->findBy(['isSentUser'=>true]);
        $bookings = $bookingRepository->findAll();
        $solutions = $solutionRepository->findAll();

        return $this->render('tracking/is_returned.html.twig', [
            'trackings'=>$trackings,
            'tracking'=>$tracking,
            'booking'=>$booking,
            'bookings'=>$bookings,
            'solutions'=>$solutions
        ]);

    }
    /**
     * @Route("bookingIsSent/{id}", name="booking_is_sent", methods={"GET"})
     * @param Booking $booking
     * @param BookingRepository $bookingRepo
     * @param TrackingRepository $trackingRepository
     * @return Response
     */
    public function bookingIsSent(Booking $booking, BookingRepository $bookingRepo, TrackingRepository $trackingRepository): Response
    {
        return $this->render('booking/is_sent.html.twig', [
            'booking' => $booking,
            'bookings' => $bookingRepo->findAll(),
            'trackings' => $trackingRepository->findAll(),
        ]);

    }
}
