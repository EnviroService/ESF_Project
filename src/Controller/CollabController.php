<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Tracking;
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

}
