<?php


namespace App\Controller;


use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("contact/", name="add_message")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response A response instance
     * @throws TransportExceptionInterface
     */
    public function add(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

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

            $this->addFlash('success', 'Ton message a été envoyé, nous te répondrons rapidement !');

            return $this->redirectToRoute('home');
        }

        return $this->render(
            'Contact/contact.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

}
