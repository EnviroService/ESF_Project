<?php


namespace App\Controller;

use App\Form\ContactType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("contact/", name="add_message")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @return Response A response instance
     * @throws TransportExceptionInterface
     */
    public function add(Request $request,
                        UserPasswordEncoderInterface $passwordEncoder,
                        UserRepository $userRepository,
                        EntityManagerInterface $entityManager,
                        MailerInterface $mailer): Response
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->setRoles(['ROLE_USER']);
            $user->setSignupDate(new DateTime('now'));
            $user->setSigninDate(new DateTime('now'));
            $user->setErpClient(0);
            $user->setJustifyDoc(1);
            $user->setBonusRateCard(0);
            $user->setBonusOption(0);
            $user->getId();


            // upload des fichiers cni et kbis
            /** @var UploadedFile $cniFile */
            $cniFile = $form->get('cni')->getData();

            $ext = $cniFile->getClientOriginalExtension();
            if ($ext != "pdf") {
                $this->addFlash('danger', "Le fichier doit être de type .pdf. 
                Format actuel envoyé: .$ext");

                return $this->redirectToRoute('add_message');
            }

            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/cni/';
            $originalFilename = pathinfo($cniFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilenameCni = $originalFilename . ".pdf";
            $cniFile->move(
                $destination,
                $newFilenameCni
            );

            $kbisFile = $form->get('kbis')->getData();

            $ext = $kbisFile->getClientOriginalExtension();
            if ($ext != "pdf") {
                $this->addFlash('danger', "Le fichier doit être de type .pdf. 
                Format actuel envoyé: .$ext");

                return $this->redirectToRoute('add_message');
            }

            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/kbis/';
            $originalFilename = pathinfo($kbisFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilenameKbis = $originalFilename . ".pdf";
            $kbisFile->move(
                $destination,
                $newFilenameKbis
            );

            $user->setCni($newFilenameCni);
            $user->setKbis($newFilenameKbis);

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            // mail for esf
            $emailESF = (new Email())
                ->from(new Address($user->getEmail(), $user->getUsername()))
                ->to(new Address('github-test@bipbip-mobile.fr', 'Enviro Services France'))
                ->replyTo($user->getEmail())
                ->subject($user->getSubject())
                ->html($this->renderView(
                    'Contact/sentMail.html.twig',
                    array('form' => $user)
                ));

            // mail for user
            $emailExp = (new Email())
                ->from(new Address('github-test@bipbip-mobile.fr', 'Enviro Services France'))
                ->to(new Address($user->getEmail(), $user->getUsername()))
                ->replyTo('github-test@bipbip-mobile.fr')
                ->subject("Votre demande d'inscription est prise en compte")
                ->html($this->renderView(
                    'Contact/inscriptionConfirm.html.twig', array('form' => $user)
                ));

            $mailer->send($emailExp);
            $mailer->send($emailESF);

            if ($form)
                $this->addFlash('success', "Votre demande d'ouverture de compte a bien été prise en compte, vous receverez un email lors de l'activation");

            return $this->redirectToRoute('index');
        }

        return $this->render('Contact/contact.html.twig',
            [
                'contactType' => $form->createView()
            ]);
    }
}