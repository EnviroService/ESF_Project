<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Solution;
use App\Entity\Tracking;
use App\Entity\User;
use App\Form\BookingType;
use App\Form\TrackingType;
use App\Repository\BookingRepository;
use App\Repository\TrackingRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

/**
 * @Route("/repair")
 * @IsGranted("ROLE_USER_VALIDATED")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/", name="booking_index", methods={"GET"})
     * @param BookingRepository $bookingRepository
     * @return Response
     */
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new/{id}", name="booking_new", methods={"GET","POST"}, defaults={"id": null})
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function new(Request $request, ?Booking $booking): Response
    {
        // si le Booking n'existe pas, on le créé
        if(empty($booking)) {
            $booking = new Booking();
            $user = $this->getUser();
            $booking->setUser($user);
        }

        // puis on crée un nouveau tracking associé au booking
        $tracking = new Tracking();
        $tracking->setBooking($booking);
        $form = $this->createForm(TrackingType::class, $tracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $solution = new Solution();
            $solution
                ->setBrand($form->get('brand')->getData())
                ->setModel($form->get('model')->getData())
                ->setSolution($form->get('solution')->getData())
                ->setPrestation('none')
                ->setPrice(0)
                ;
            $tracking
                ->addSolution($solution)
                ->setIsSent(0)
                ->setIsReceived(0)
                ->setIsRepaired(0)
                ->setIsReturned(0);
            $booking
                ->setDateBooking(new DateTime())
                ->setIsReceived(0)
                ->setIsSent(0)
                ->setIsSentUser(0)
            ;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->persist($tracking);
            $entityManager->persist($solution);
            $entityManager->flush();

            return $this->redirectToRoute('booking_new', [
                'id' => $booking->getId(),
            ]);
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_show", methods={"GET"})
     * @param Booking $booking
     * @param BookingRepository $bookingRepo
     * @param TrackingRepository $trackingRepository
     * @return Response
     */
    public function show(Booking $booking, BookingRepository $bookingRepo, TrackingRepository $trackingRepository): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'bookings' => $bookingRepo->findAll(),
            'trackings' => $trackingRepository->findAll(),
        ]);

    }


    /**
     * @Route("/{id}/edit", name="booking_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Booking $booking
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, Booking $booking, User $user): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_index',[
                'id'=>$user->getId()
            ]);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'user'=> $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_delete", methods={"DELETE"})
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function delete(Request $request, Booking $booking): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('booking_index');
    }
}
