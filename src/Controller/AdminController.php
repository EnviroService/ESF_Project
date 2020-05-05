<?php

namespace App\Controller;

use App\Entity\Tracking;
use App\Entity\User;
use App\Entity\Options;
use App\Entity\RateCard;
use App\Form\OptionsType;
use App\Form\RateCardType;
use App\Form\RegistrationCollaboratorFormType;
use App\Form\RegistrationFormType;
use App\Form\UserEditType;
use App\Repository\BookingRepository;
use App\Repository\OptionsRepository;
use App\Repository\RateCardRepository;
use App\Repository\TrackingRepository;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @Route("/admin")
 */

class AdminController extends AbstractController
{
    private $users;

    public function __construct(UserRepository $uRepo)
    {
        $this->users = $uRepo->findAll();
    }

    /**
     * @Route("/", name="admin")
     * @param Request $request
     * @param TrackingRepository $trackRepo
     * @param BookingRepository $bookingRepo
     * @return Response
     */
    public function adminIndex(Request $request, TrackingRepository $trackRepo, BookingRepository $bookingRepo)
    {
        // read last update dates
        $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/';
        $file = fopen($destination . 'ratecards/last_ratecard.txt', "r");
        $update_ratecard = fgets($file, 100);
        fclose($file);
        $file = fopen($destination . 'options/last_options.txt', "r");
        $update_options = fgets($file, 100);
        fclose($file);

        $trackings = $trackRepo->findAll();
        $bookings = $bookingRepo->findBy(['isSent'=>true]);

        return $this->render('admin/index.html.twig', [
            'users' => $this->users,
            'trackings' => $trackings,
            'bookings' => $bookings,
            'update_ratecard' => $update_ratecard,
            'update_options' => $update_options,
        ]);
    }

    /**
     * @Route("/users", name="admin-users")
     * @IsGranted("ROLE_ADMIN")
     * @param UserRepository $uRepo
     * @return Response
     */
    public function allowUsers(UserRepository $uRepo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $this->users]);

    }

    /**
     * @Route("/users/{id}/status", name="user-edit-status", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function editProfil(Request $request,
                               User $user,
                               EntityManagerInterface $entityManager,
                               MailerInterface $mailer): Response
    {

        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(['ROLE_USER_VALIDATED']);
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();


            $this->addFlash('success', "L'inscription est prise en compte un mail va etre envoyé à votre client");

            // Envoi de mail aprés acceptation

            $subjectUser = "Votre demande d'inscription a été acceptée, votre compte est désormais actif. Bienvenue chez Enviro Services France";


            // mail for user
            $emailExp = (new Email())
                ->from(new Address('github-test@bipbip-mobile.fr', 'Enviro Services France'))
                ->to(new Address($user->getEmail(), $user->getUsername()))
                ->replyTo('github-test@bipbip-mobile.fr')
                ->subject($subjectUser)
                ->html($this->renderView(
                    'Contact/sentMailUserActivation.html.twig', array('user' => $user)
                ));

            $mailer->send($emailExp);

            return $this->redirectToRoute('admin-users');
        }

        return $this->render('admin/userStatus.html.twig', [
            'id' => $user->getId(),
            'user' => $user,
            'UserEditForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/{id}/status", name="users_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin-users');
    }

    /**
     * @Route("/admin/ratecard", name="admin-ratecard")
     * @param Request $request
     * @param RateCardRepository $rateCards
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function uploadRateCard(
        Request $request,
        RateCardRepository $rateCards,
        EntityManagerInterface $em
    )

    {
        // create form
        $form = $this->createForm(RateCardType::class);
        $form->handleRequest($request);
        $log = [];
        $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/ratecards/';

        // verify data after submission
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $rateCardFile */
            $rateCardFile = $form->get('rateCard')->getData();

            // verify extension format
            $ext = $rateCardFile->getClientOriginalExtension();
            if ($ext != "csv") {
                $this->addFlash('danger', "Le fichier doit être de type .csv. 
                Format actuel envoyé: .$ext");

                return $this->redirectToRoute('admin-ratecard');
            }

            // save file on server
            $originalFilename = pathinfo($rateCardFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . ".csv";
            $rateCardFile->move(
                $destination,
                $newFilename
            );

            // drop lines already in table
            $oldRateCards = $rateCards->findAll();
            foreach ($oldRateCards as $line) {
                $em->remove($line);
            }

            // prepare log of upload
            $errors = 0;
            $success = 0;
            $number = "nombre mal formaté. Attendu > nombre entier ou décimal (à virgule)";
            $empty = "ce champ est vide";

            // open the file to put data in DB and make the log
            $csv = fopen($destination . $newFilename, 'r');
            $i = 0;
            while (($data = fgetcsv($csv, 0, ';')) !== FALSE) {
                if ($i != 0) {
                    $bug = 0;

                    $rateCard = new RateCard();

                    $price = str_replace(' ', '', $data[4]);
                    $price = floatval(str_replace(',', '.', $price));
                    if (is_float($price) == false) {
                        array_push($log, "Ligne $i : $number");
                        $bug = 1;
                        $errors++;
                        $price = 0;
                    } elseif ($price == 0) {
                        array_push($log, "Ligne $i : prix > $empty ou $number");
                        $bug = 1;
                        $errors++;
                    }

                    $price = number_format($price, 2);
                    $rateCard->setPriceRateCard($price);

                    if (empty($data[0])) {
                        array_push($log, "Ligne $i : marque > $empty");
                        $bug = 1;
                        $errors++;
                    }
                    $rateCard->setBrand($data[0]);

                    if (empty($data[1])) {
                        array_push($log, "Ligne $i : modèle > $empty");
                        $bug = 1;
                        $errors++;
                    }
                    $rateCard->setModels($data[1]);

                    if (empty($data[2])) {
                        array_push($log, "Ligne $i : prestation > $empty");
                        $bug = 1;
                        $errors++;
                    }
                    $rateCard->setPrestation($data[2]);

                    if (empty($data[3])) {
                        array_push($log, "Ligne $i : solution > $empty");
                        $bug = 1;
                        $errors++;
                    }
                    $rateCard->setSolution($data[3]);

                    if ($bug != 1) {
                        $em->persist($rateCard);
                        $success++;
                    }
                }
                $i++;
            }

            $em->flush();

            // send confirmations
            $this->addFlash(
                'success',
                "$success lignes correctement ajoutées"
            );
            if ($errors > 0) {
                $this->addFlash(
                    'danger',
                    "$errors erreurs trouvées (voir log ci-dessous)"
                );
            }

            // log the update date
            file_put_contents($destination . 'last_ratecard.txt', date("d/m/Y à H:i"));
        }

        // read last update date
        $file = fopen($destination . 'last_ratecard.txt', "r");
        $update = fgets($file, 100);
        fclose($file);

        // find all lines in rateCards
        $rateCards = $rateCards->findAll();

        return $this->render('admin/ratecard.html.twig', [
            'form' => $form->createView(),
            'rateCards' => $rateCards,
            'logs' => $log,
            'update' => $update,
        ]);
    }


    /**
     * @Route("/admin/options", name="admin-options")
     * @param Request $request
     * @param OptionsRepository $options
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function uploadOptions(
        Request $request,
        OptionsRepository $options,
        EntityManagerInterface $em
    )
    {
        // create form
        $form = $this->createForm(OptionsType::class);
        $form->handleRequest($request);
        $log = [];
        $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/options/';

        // verify data after submission
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $optionsFile */
            $optionsFile = $form->get('options')->getData();

            // verify extension format
            $ext = $optionsFile->getClientOriginalExtension();
            if ($ext != "csv") {
                $this->addFlash('danger', "Le fichier doit être de type .csv. 
                Format actuel envoyé: .$ext");

                return $this->redirectToRoute('admin-options');
            }

            // save file on server
            $originalFilename = pathinfo($optionsFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . ".csv";
            $optionsFile->move(
                $destination,
                $newFilename
            );

            // drop lines already in table
            $oldOptions = $options->findAll();
            foreach ($oldOptions as $line) {
                $em->remove($line);
            }

            // prepare log of upload
            $errors = 0;
            $success = 0;
            $number = "nombre mal formaté. Attendu > nombre entier ou décimal (à virgule)";
            $empty = "ce champ est vide";

            // open the file to put data in DB
            $csv = fopen($destination . $newFilename, 'r');
            $i = 0;
            while (($data = fgetcsv($csv)) !== FALSE) {
                if ($i != 0) {
                    $bug = 0;
                    $option = new Options();

                    if (empty($data[0])) {
                        array_push($log, "Ligne $i : description > $empty");
                        $bug = 1;
                        $errors++;
                    }
                    $option->setDescription($data[0]);

                    $price = str_replace(' ', '', $data[1]);
                    $price = floatval(str_replace(',', '.', $price));
                    if (is_float($price) == false) {
                        array_push($log, "Ligne $i : $number");
                        $bug = 1;
                        $errors++;
                        $price = 0;
                    } elseif ($price == 0) {
                        array_push($log, "Ligne $i : prix > $empty ou $number");
                        $bug = 1;
                        $errors++;
                    }
                    $price = number_format($price, 2);
                    $option->setPriceOption($price);

                    if ($bug != 1) {
                        $em->persist($option);
                        $success++;
                    }
                }
                $i++;
            }

            $em->flush();

            // send confirmations
            $this->addFlash(
                'success',
                "$success lignes correctement ajoutées"
            );
            if ($errors > 0) {
                $this->addFlash(
                    'danger',
                    "$errors erreurs trouvées (voir log ci-dessous)"
                );
            }

            // log the update date
            file_put_contents($destination . 'last_options.txt', date("d/m/Y à H:i"));
        }

        // read last update date
        $file = fopen($destination . 'last_options.txt', "r");
        $update = fgets($file, 100);
        fclose($file);

        // find all lines in Options
        $options = $options->findAll();

        return $this->render('admin/options.html.twig', [
            'form' => $form->createView(),
            'options' => $options,
            'logs' => $log,
            'update' => $update,
        ]);
    }

    /**
     * @Route("/collaborator/register", name="register_collaborator")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     */
    public function registerCollaborator(Request $request,
                                         UserPasswordEncoderInterface $passwordEncoder,
                                         GuardAuthenticatorHandler $guardHandler,
                                         LoginFormAuthenticator $authenticator
    )
    {
        $user = new User();

        $form = $this->createForm(RegistrationCollaboratorFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_COLLABORATOR'])
                ->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                ))
                ->setUsername("testuser")
                ->setSIRET(12345678912345)
                ->setNumTVA("fr1234567891234")
                ->setEmail($form->get('email'))
                ->setBillingAddress("3 rue")
                ->setBillingPostcode(59000)
                ->setBillingCity("lille")
                ->setJustifyDoc(0)
                ->setRefContact(0)
                ->setOperationalAddress("3 rue")
                ->setOperationalCity("lille")
                ->setOperationalPostcode(59000)
                ->setBossName("test")
                ->setSigninDate(new \DateTime('now'))
                ->setSignupDate(new \DateTime('now'))
                ->setErpClient(0)
                ->setKbis("FR12345678912")
                ->setCni(0)
                ->setBonusOption(0)
                ->setBonusRateCard(0)
                ->setNumPhone(0)
                ->setEnseigne("Enseigne test");


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'inscription est prise en compte un mail va etre envoyé à votre client");

            return $this->redirectToRoute("admin");
        }
        return $this->render('admin/collaborator.html.twig', [
            'RegistrationCollaboratorFormType' => $form->createView(),
        ]);

    }
}

