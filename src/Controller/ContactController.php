<?php


namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response A response instance
     * @throws \Exception
     */
    public function add(Request $request,
                        UserPasswordEncoderInterface $passwordEncoder,
                        UserRepository $userRepository,
                        EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(registrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->setRoles(['ROLE_USER']);
            $user->setSignupDate(new DateTime('now'));
            $user->setSigninDate(new DateTime('now'));
            $user->setErpClient(0);
            $user->setJustifyDoc(1);
            $user->setRefContact(0);

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

                return $this->redirectToRoute('app_register');
            }

            $destination = $this->getParameter('kernel.project_dir').'/public/uploads/cni/';
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

                return $this->redirectToRoute('app_register');
            }

            $destination = $this->getParameter('kernel.project_dir').'/public/uploads/kbis/';
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

            if ($form)
                $this->addFlash('success', "Votre demande d'ouverture de compte a bien été prise en compte, vous receverez un email lors de l'activation");

            return $this->redirectToRoute('index');

        }

        return $this->render('Contact/contact.html.twig', [
            'contactType' => $form->createView(),
        ]);

            /*
            // mail for ESF
            $emailEsf = (new Email())
                ->from(new Address($contactFormData->getMail(), $contactFormData
                        ->getFirstname() . ' ' . $contactFormData->getLastname()))
                ->to(new Address('github-test@bipbip-mobile.fr', 'Enviro Service France'))
                ->replyTo($contactFormData->getMail())
                ->subject($contactFormData->getMessage())
                ->html($this->renderView(
                    'contact/sent_mail.html.twig',
                    array('form' => $contactFormData)
                ));

            // send a copy to sender
            $emailExp = (new Email())
                ->from(new Address('nepasrepondre@esf.fr', 'Enviro Service France'))
                ->to(new Address($contactFormData->getMail(), $contactFormData
                        ->getFirstname() . ' ' . $contactFormData->getLastname()))
                ->replyTo('nepasrepondre@esf.fr')
                ->subject('Votre message envoyé à Enviro Service France')
                ->html($this->renderView(
                    'contact/exp_mail.html.twig',
                    array('form' => $contactFormData)
                ));

            $mailer->send($emailEsf);
            $mailer->send($emailExp);
*/
    }

}
