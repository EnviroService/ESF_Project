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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
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

    // montre tous les téléphones et leur état
    /**
     * @Route("/", name="tracking_index", methods={"GET"})
     * @IsGranted("ROLE_COLLABORATOR")
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

    // CRUD Création d'un nouveau tracking (téléphone à suivre)
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

    // // Vérification du téléphone et possibilité de changer les états
    // (vérification IMEI, statut réparé, statut renvoyé)
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

    // définit l'envoi comme reçu et envoie un message au client
    /**
     * @Route("/received/{id}", name="received_tracking", methods={"GET"})
     * @param Tracking $tracking
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    public function isReceived(Tracking $tracking, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $booking = $tracking->getBooking();
        $tracking ->setIsReceived(true)
                ->setReceivedDate(new DateTime('now'));

        $user = $this->getUser();

        $entityManager->persist($tracking);
        $entityManager->flush();

        //Mailing
        $subjectUser ="Votre colis a été reçu";

        $emailExp = (new Email())
            ->from(new Address('github-test@bipbip-mobile.fr', 'Enviro Services France'))
            ->to(new Address($booking->getUser()->getEmail(), $booking->getUser()->getUsername()))
            ->replyTo('github-test@bipbip-mobile.fr' )
            ->subject($subjectUser)
            ->html($this->renderView(
                'Contact/sentMailTrackingisReceived.html.twig', array('user' => $user)
            ));

        $mailer->send($emailExp);


        return $this->redirectToRoute('tracking_show', [
            'id' => $booking->getId(),
        ]);
    }

    // Fait passer le statut du téléphone à "en réparation"
    /**
     * @Route("/repaired/{id}", name="repaired_tracking", methods={"GET"})
     * @param Tracking $tracking
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    public function isRepaired(Tracking $tracking, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $booking = $tracking->getBooking();
        $tracking ->setIsRepaired(true)
                  ->setRepairedDate(new DateTime('now'));

        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($tracking);
        $entityManager->flush();


        //Mailing
        $subjectUser ="Vos téléphones sont dans nos ateliers afin d'etre réparés";

        $emailExp = (new Email())
            ->from(new Address('github-test@bipbip-mobile.fr', 'Enviro Services France'))
            ->to(new Address($booking->getUser()->getEmail(), $booking->getUser()->getUsername()))
            ->replyTo('github-test@bipbip-mobile.fr' )
            ->subject($subjectUser)
            ->html($this->renderView(
                'Contact/sentMailIsRepaired.html.twig', array('user' => $user)
            ));

        $mailer->send($emailExp);


        return $this->redirectToRoute('tracking_show', [
            'id' => $booking->getId(),
        ]);
    }

    // CRUD édition d'un tracking
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

    // CRUD suppression d'un tracking
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
