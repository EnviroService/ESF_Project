<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Factures;
use App\Repository\BookingRepository;
use App\Repository\FacturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;


/**
 * @Route("/admin")
 */

class FacturesController extends AbstractController
{

    /**
     * @Route("/return/{id}", name="booking_return", defaults={"id":null})
     * @IsGranted("ROLE_COLLABORATOR")
     * @param Booking $booking
     * @param BookingRepository $bookings
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function return(?Booking $booking, BookingRepository $bookings, EntityManagerInterface $em): Response
    {
        if (!empty($booking)) {
            // envoi du booking
            $booking->setIsSent(1);
            // creation de la facture
            $facture = new Factures();
            // TODO : calcul du total des réparations HT
            $total = 200;
            $facture
                ->setUser($booking->getUser())
                ->setBooking($booking)
                ->setDateEdit(new DateTime())
                ->setIsPaid(0)
                ->setAmount($total)
                ;
            $em->persist($booking);
            $em->persist($facture);
            $em->flush();

            // Configure Dompdf
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            // Instantiate Dompdf
            $dompdf = new Dompdf();

            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('factures/facture.html.twig', [
                'facture' => $facture,
            ]);

            // Load HTML to Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Store PDF Binary Data
            $output = $dompdf->output();

            // In this case, we want to write the file in the public directory
            $publicDirectory = 'uploads/factures';
            $filename = "F" . $facture->getId() . "C" . $facture->getUser()->getId();
            $pdfFilepath =  $publicDirectory . '/' . $filename . '.pdf';

            // Write file to the desired path
            file_put_contents($pdfFilepath, $output);

            $this->addFlash('success', "Facture générée");

            return $this->redirectToRoute('factures', [
                'id' => $facture->getId(),
            ]);
        }
        $bookings = $bookings->findBy([
            'isReceived'=>true,
            'isSent'=>false,
        ]);
        return $this->render('admin/returnToClient.html.twig', [
            'booking' => $booking,
            'bookings' => $bookings,
        ]);
    }

    /**
     * @Route("/facture/{id}", name="factures", defaults={"id":null})
     * @IsGranted("ROLE_COLLABORATOR")
     * @param Factures $facture
     * @param FacturesRepository $factures
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function facture(?Factures $facture, FacturesRepository $factures, EntityManagerInterface $em): Response
    {
        if (!empty($facture)) {
            return $this->render('factures/show.html.twig', [
                'facture' => $facture,
            ]);
        }
        $factures = $factures->findAll();
        return $this->render('factures/index.html.twig', [
            'factures' => $factures,
        ]);
    }
}
