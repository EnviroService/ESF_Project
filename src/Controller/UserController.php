<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Simulation;
use App\Entity\Tracking;
use App\Entity\User;
use App\Form\EditContactType;
use App\Form\InfoUserEditType;
use App\Repository\BookingRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\functionGenerale;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{id}", name="user_show", methods={"GET","POST"})
     * @IsGranted("ROLE_USER_VALIDATED")
     * @param User $user
     * @param BookingRepository $bookings
     * @param functionGenerale $functionGenerale
     * @return Response
     */

    public function showUser(User $user,
                             BookingRepository $bookings,
                             functionGenerale $functionGenerale
    ): Response
    {
        if ($this->getUser() == $user) {
            $id = $user->getId();
            $enseignes = $user->getEnseigne();
            $bookings = $bookings->findBy(['user'=>$user]);
            $functionGenerale->discardDevisEmpty($user);
            return $this->render('user/showUser.html.twig', [
                'enseignes' => $enseignes,
                'user' => $user,
                'id' => $id,
                'bookings' => $bookings,
            ]);
        } else {

            $this->addFlash('danger', 'Vous devez être connecté pour voir vos informations');
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/add/{id}", name="message", methods={"GET", "POST"})
     * @param Request $request
     * @param MailerInterface $mailer
     * @param User $user
     * @return Response A response instance
     * @throws TransportExceptionInterface
     */
    public function add(Request $request, MailerInterface $mailer, User $user): Response
    {

        $form = $this->createForm(EditContactType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            //dd($contactFormData);

            // mail for ESF

            $subject = "Demande de modifications d'informations sur ESF";
            $subjectUser ="Votre demande de modification sur votre compte";

            $emailESF = (new Email())
                ->from(new Address($user->getEmail(), $user->getUsername()))
                ->to(new Address('github-test@bipbip-mobile.fr', 'Enviro Services France'))
                ->replyTo($user->getEmail())
                ->subject($subject)
                ->html($this->renderView(
                    'Contact/sentMail.html.twig',
                    array('user' => $user,
                        'subject' => $subject,
                        'message'=> $contactFormData->getMessage())
                ));

            // mail for user
            $emailExp = (new Email())
                ->from(new Address('github-test@bipbip-mobile.fr', 'Enviro Services France'))
                ->to(new Address($user->getEmail(), $user->getUsername()))
                ->replyTo('github-test@bipbip-mobile.fr' )
                ->subject($subjectUser)
                ->html($this->renderView(
                    'Contact/inscriptionConfirm.html.twig', array('user' => $user,
                        'subjectUser' => $subjectUser)
                ));
            $mailer->send($emailExp);
            $mailer->send($emailESF);

            $this->addFlash('success', 'Votre message a été envoyé, nous vous répondrons rapidement !');

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'user/edit.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="user_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param User $user
     * @return Response
     */

    public function edit(Request $request, User $user) :Response
    {
        $form = $this->createForm(InfoUserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La modification a été bien éffectuée');

            return $this->redirectToRoute('index', [
                'user' => $user
            ]);
        }

        return $this->render('user/infoEdit.html.twig', [
            'contactForm' => $form->createView(),
            'user' => $user,
        ]);
    }


    /**
     * @Route("/{id}/panier", name="show_panier")
     * @param User $user
     * @return Response
     */
    public function showPanier(User $user)
    {
        // Récupérer les devis user
        $devis = $user->getDevis();
        $simus = [];

        // Recherche des simulations appartenant au devis non validé
        foreach ($devis as $devi){
            if ($devi->getIsValidated() == null || $devi->getIsValidated() == false){
                $simus[] = $devi->getSimulations();
            }
        }

        return $this->render("user/panier.html.twig", [
            'user' => $user,
            'simulations' => $simus,
            'devis' => $devis
        ]);
    }

    /**
     * @Route("/{id}/delete_simulation", name="delete_simulation")
     * @param EntityManagerInterface $em
     * @param Simulation $simulation
     * @return RedirectResponse
     */
    public function deleteSimulation(
        EntityManagerInterface $em,
        Simulation $simulation
    )
    {
        $em->remove($simulation);
        $em->flush();

        return $this->redirectToRoute("show_panier", [
            'id' => $this->getUser()->getId()
        ]);
    }

    /**
     * @Route("/{id}/valid_panier", name="valid_panier")
     * @param EntityManagerInterface $em
     * @param User $user
     * @param NormalizerInterface $normalizer
     * @return string
     * @throws ExceptionInterface
     */
    public function validPanier(
        EntityManagerInterface $em,
        User $user,
        NormalizerInterface $normalizer
    )
    {
        // Création du nouveau booking
        $booking = new Booking();
        // Attribution des propriétés essentielles au booking
        $booking
            ->setUser($user)
            ->setIsSentUser(true)
            ->setIsReceived(false)
            ->setIsSent(false)
            ->setDateBooking(new DateTime('now'))
            ->setSentUserDate(new DateTime("now"))
        ;
        // Récupération des IMEI's
        foreach ($_GET['IMEI'] as $IMEI){
            //Création du tracking correspondant au téléphone
            $tracking = new Tracking();
            // Attribution des propriétés essentielles du tracking
            $tracking
                ->setBooking($booking)
                ->setImei($IMEI)
                ->setIsSent(true)
                ->setIsReceived(false)
                ->setIsRepaired(false)
                ->setIsReturned(false)
                ->setSentDate(new DateTime("now"))
            ;
            // Ajout du tracking dans le booking
            $booking
                ->addTracking($tracking)
            ;
            $em->persist($tracking);
            $em->persist($booking);
        }
        $em->flush();

        // Essaie de la normalisation du booking pour génération de fichier csv.
        try {
            // transformation du booking en tableau, prenant en compte les valeurs du group 'booking'.
            $book = $normalizer->normalize($booking, "json", ["groups" => "booking"]);
            // Création du nom de fichier
            $filename = "b_" . $booking->getId();
            // Chemin du repertoire contenant les fichiers csv
            $repertory = "uploads/booking/";
            // Création de l'extension
            $ext = ".csv";
            // Transformation du tableau book en json
            $json = json_encode($book);

            // Chemin complet du fichier csv booking
            $file = $repertory . $filename . $ext;
            // Création si le fichier n'existe pas pour écriture du book
            $openFile = fopen($file, "w+");
            // Ecriture du book
            fwrite($openFile, $json);
            // Fermeture du fichier.
            fclose($openFile);

            // Faire passer les devis en status 'validated' pour éviter de les revoir dans le panier.
            $devis = $user->getDevis();
            foreach ($devis as $devi){
                $devi->setIsValidated(true);
                $em->persist($devi);

            }
            $em->flush();

            return $this->redirectToRoute("show_panier", [
                'id' => $user->getId()
            ]);

        } catch (\Exception $e){
            // En cas d'echec, faire apparaitre le message d'erreur
            $error = $e->getMessage();
            $code = $e->getCode();
            $message = "message d'erreur: $error<br> code erreur: $code";
            $response = new Response($message, 200);

            return $response;
        }
    }

    /*

    /**
     * @Route("booking/{id}", name="user_received_booking", methods={"GET"})
     * @param Booking $booking
     * @return RedirectResponse
     */
   /* public function isReceivedForUser(Booking $booking): Response
    {
       $booking = $this->getDoctrine()
           ->getRepository(Booking::class)
           ->findBy(['']);

        return $this->redirectToRoute
    }
*/

}

