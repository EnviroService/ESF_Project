<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\EditContact;
use App\Entity\Simulation;
use App\Entity\User;
use App\Form\EditContactType;
use App\Form\InfoUserEditType;
use App\Form\RegistrationFormType;
use App\Repository\BookingRepository;
use App\Repository\DevisRepository;
use App\Repository\SimulationRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $devis = $user->getDevis();
        $simus = [];

        foreach ($devis as $devi){
            $simus[] = $devi->getSimulations();
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
     * @param User $user
     * @param EntityManagerInterface $em
     * @param SimulationRepository $simulationRepo
     * @return RedirectResponse
     */
    public function validPanier(
        EntityManagerInterface $em,
        SimulationRepository $simulationRepo,
        User $user
    )
    {
        $devis = $user->getDevis()->getValues();
        foreach ($devis as $devi){
            $simulations = $devi->getSimulations()->getValues();
            foreach ($simulations as $simulaton){
                $simulaton->setDevis($devi);
                $em->persist($simulaton);
            }
            $em->flush();
        }

        /*
        $devis = $user->getDevis()->getValues();
        foreach ($devis as $devi){
            $simulations[] = $devi->getSimulations()->getValues();
            foreach ($simulations as $simulation){
                $simulations
            }
            dd($devis);
        }*/
        // Debug pour $devis
            /*
            foreach ($devis as $devi){
                dump($devi);
            }*/

        // Boucle sur le tableau comportant les tableaux d'id des simulations
      /*  foreach ($_GET['simulationId'] as $id){
            // Recherche de la simulation
            $searchSimulation = $simulationRepo->find([
                'id' => $id
                ]
            );
            // Changer le status de la simulation pour la passer en is_validated.
            $searchSimulation->setIsValidated(true);
            // Prendre le devis de la simulation pour supprimer la simulation
            // car elle est Validated pour le devis final
            $devis = $searchSimulation->getDevis();
            $devis->removeSimulation($searchSimulation);
            $em->persist($devis);
            // On attribu le devis final a la simulation
            $searchSimulation->setDevis($devisPanier);
            $em->persist($searchSimulation);

            // Ajout de la simulation du panier, dans le devis final afin de bien raccorder les deux.
            $devisPanier->addSimulation($searchSimulation);
            $em->persist($devisPanier);
        }
        //$em->flush();*/

        return $this->redirectToRoute("show_panier", [
            'id' => $user->getId()
            ]
        );


    }
}

