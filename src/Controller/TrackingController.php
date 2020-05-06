<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Tracking;
use App\Entity\User;
use App\Form\TrackingType;
use App\Repository\BookingRepository;
use App\Repository\TrackingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tracking")

 */
class TrackingController extends AbstractController
{
    private $trackings;

    public function __construct(TrackingRepository $trackingRepo)
    {
        $this->trackings = $trackingRepo->findAll();
    }

    /**
     * @Route("/", name="tracking_index", methods={"GET"})
     * @param BookingRepository $bookingRepo
     * @return Response
     */
    public function index(BookingRepository $bookingRepo): Response
    {
        return $this->render('tracking/index.html.twig', [
            'trackings' => $this->trackings,
            'bookings' => $bookingRepo->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="tracking_new", methods={"GET","POST"}, defaults={"id": null})
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function new(Request $request, Booking $booking, TrackingRepository $trackRepo): Response
    {

        $tracking = $trackRepo->findOneBy(['booking' => $booking]);
        $form = $this->createForm(TrackingType::class, $tracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            {
                $tracking->setIsSent($form->get('IsSent')->getData())
                    ->setIsReceived(0)
                    ->setIsRepaired(0)
                    ->setIsReturned(0)
                    ->setBooking($booking);


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tracking);
                $entityManager->flush();


                return $this->redirectToRoute('tracking_index');
            }
        }
            return $this->render('tracking/new.html.twig', [
                'tracking' => $tracking,
                'form' => $form->createView(),
            ]);

    }

    /**
     * @IsGranted("ROLE_COLLABORATOR")
     * @Route("/show/{id}", name="tracking_show", methods={"GET","POST"})
     * @param Tracking $tracking
     * @param Request $request
     * @param BookingRepository $bookingRepo
     * @return Response
     */
    public function show(Tracking $tracking,
                         Request $request,
                         BookingRepository $bookingRepo
    ): Response
    {

        $solutions =$tracking->getSolutions();
        $booking = $tracking ->getBooking();
        $bookings= $bookingRepo->findAll();

        return $this->render('tracking/show.html.twig', [
            'id' => $tracking->getId(),
            'tracking' => $tracking,
            'trackings' =>$this->trackings,
            'booking' => $booking,
            'bookings' => $bookings,
            'solutions' => $solutions

        ]);

}

    /**
     * @Route("/received/{id}", name="received_tracking", methods={"GET"})
     * @param Tracking $tracking
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function isReceived(Tracking $tracking, EntityManagerInterface $entityManager): Response
    {
        $booking = $tracking->getBooking();
        $tracking ->setIsReceived(true)
                ->setReceivedDate(new DateTime('now'));

        $entityManager->persist($tracking);
        $entityManager->flush();

        return $this->redirectToRoute('tracking_show', [
            'id' => $booking->getId(),
        ]);
    }

    /**
     * @Route("/repaired/{id}", name="repaired_tracking", methods={"GET"})
     * @param Tracking $tracking
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function isRepaired(Tracking $tracking, EntityManagerInterface $entityManager): Response
    {
        $booking = $tracking->getBooking();
        $tracking ->setIsRepaired(true)
                  ->setRepairedDate(new DateTime('now'));


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($tracking);
        $entityManager->flush();

        return $this->redirectToRoute('tracking_show', [
            'id' => $booking->getId(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tracking_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Tracking $tracking
     * @param BookingRepository $bookingRepo
     * @return Response
     */
    public function edit(Request $request, Tracking $tracking, BookingRepository $bookingRepo): Response
    {

        $form = $this->createForm(TrackingType::class, $tracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tracking_index');
        }

        return $this->render('tracking/edit.html.twig', [
            'tracking' => $tracking,
            'trackings' => $this->trackings,
            'bookings' => $bookingRepo->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tracking_delete", methods={"DELETE"})
     * @param Request $request
     * @param Tracking $tracking
     * @return Response
     */
    public function delete(Request $request, Tracking $tracking): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tracking->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tracking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tracking_index');
    }

}
