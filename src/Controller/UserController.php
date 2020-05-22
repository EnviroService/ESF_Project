<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Simulation;
use App\Entity\Tracking;
use App\Entity\User;
use App\Form\EditContactType;
use App\Form\InfoUserEditType;
use App\Repository\BookingRepository;
use App\Repository\SimulationRepository;
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
     * @param Request $request
     * @return Response
     */
    public function showPanier(User $user, Request $request)
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
     * @param SimulationRepository $simulationRepo
     * @return string
     */
    public function validPanier(
        EntityManagerInterface $em,
        User $user,
        SimulationRepository $simulationRepo
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

        // Récupération des IMEI's et attribution de l'id simulation au tracking
        foreach ($_GET as $idSimu => $value){
            $simulation = $simulationRepo->findOneBy(
            [
                'id' => $idSimu
            ]
            );

            foreach ($value as $IMEI) {
                //Création du tracking correspondant au téléphone
                $tracking = new Tracking();
                // Attribution des propriétés essentielles du tracking
                $tracking
                    ->setBooking($booking)
                    ->setImei($IMEI)
                    ->setSimulation($simulation)
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
                ;
                // Données demandées pour le fichier csv
                $data = [
                    "customer" => $user->getErpClient(),
                    "Model" => "UpdateModel",
                    "Serial Number" => $IMEI,
                    "Reference Number" => "EsfId_" . $user->getId(),
                    "WO code" => "",
                    "PROGRAM" => "",
                    "WO TYPE" => "",
                    "WO INITIAL STATUS" => "",
                    "GROUP" => "",
                    "SITE" => "",
                    "PURCHASE DATE" => "",
                    "Reported Issue" => $simulation->getRatecard()->getSolution() . " - " . $simulation->getRatecard()->getPrestation(),
                    "Customer_Info_1" => $simulation->getRatecard()->getModels(),
                    "Customer_Info_2" => "",
                    "Customer_Info_3" => ""
                 ];

                // Essaie de la normalisation du booking pour génération de fichier csv.
                try {
                    $date = new DateTime("now");
                    // Création du nom de fichier
                    $filename = "ESF_web_" . $date->format("d-m-Y");
                    // Chemin du repertoire contenant les fichiers csv
                    $repertory = "uploads/booking/";
                    // Création de l'extension
                    $ext = ".csv";
                    // Chemin complet du fichier csv booking
                    $file = $repertory . $filename . $ext;

                    // Si le fichier n'existe pas, on le crée et on indique les noms des champs
                    if (!file_exists($file)){
                        $premiereLgneCSV = [
                            "customer" => "customer" ,
                            "Model" => "Model",
                            "Serial Number" => "Serial Number",
                            "Reference Number" => "Reference Number",
                            "WO code" => "WO code",
                            "PROGRAM" => "PROGRAM",
                            "WO TYPE" => "WO TYPE",
                            "WO INITIAL STATUS" => "WO INITIAL STATUS",
                            "GROUP" => "GROUP",
                            "SITE" => "SITE",
                            "PURCHASE DATE" => "PURCHASE DATE",
                            "Reported Issue" => "Reported Issue",
                            "Customer_Info_1" => "Customer_Info_1",
                            "Customer_Info_2" => "Customer_Info_2",
                            "Customer_Info_3" => "Customer_Info_3"
                        ];
                        $openFile = fopen($file, "a+");
                        fputcsv($openFile, $premiereLgneCSV);
                        fclose($openFile);
                    }
                    // Ouverture du fichier csv.
                    $openFile = fopen($file, "a+");
                    // Ecriture du track
                    fputcsv($openFile, $data);
                    // Fermeture du fichier.
                    fclose($openFile);
                } catch (\Exception $e){
                    // En cas d'echec, faire apparaitre le message d'erreur
                    $error = $e->getMessage();
                    $code = $e->getCode();
                    $message = "message d'erreur: $error<br> code erreur: $code";
                    $response = new Response($message, 200);

                    return $response;
                }
            }
        }

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

