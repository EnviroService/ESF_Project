<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditContactType;
use App\Form\InfoUserEditType;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
                        'form'=> $contactFormData)
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
     * @param Request $request
     * @param User $user
     * @return Response
     */

    public function edit(Request $request, User $user) :Response
    {
        $form = $this->createForm(InfoUserEditType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('index', [
                'user' => $user,
                'data' => $data
            ]);
        }

        return $this->render('user/infoEdit.html.twig', [
            'contactForm' => $form->createView(),
            'user' => $user
        ]);
    }
}

