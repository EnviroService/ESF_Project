<?php

namespace App\Controller;

use App\Entity\Options;
use App\Entity\RateCard;
use App\Form\OptionsType;
use App\Form\RateCardType;
use App\Repository\OptionsRepository;
use App\Repository\RateCardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/users", name="admin-users")
     */
    public function allowUsers()
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/admin/ratecard", name="admin-ratecard")
     * @param Request $request
     * @param RateCardRepository $rateCards
     * @param EntityManagerInterface $em
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */

    public function uploadRatecard(
        Request $request,
        RateCardRepository $rateCards,
        EntityManagerInterface $em
    )

    {
        // create form
        $form = $this->createForm(RateCardType::class);
        $form->handleRequest($request);

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
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads/ratecards/';
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

            // open the file to put data in DB
            $csv = fopen($destination . $newFilename,'r');
            $i = 0;
            while ( ($data = fgetcsv($csv) ) !== FALSE ) {
                if($i != 0) {
                    $rateCard = new RateCard();
                    $rateCard ->setBrand($data[0])
                              ->setModels($data[1])
                              ->setPrestation($data[2])
                              ->setSolution($data[3])
                              ->setPriceRateCard($data[4]);
                        /*->setSolution($data[0])
                        ->setPrestation($data[1])
                        ->setModels($data[2])
                        ->setPriceRateCard($data[3])
                        ->setBrand($data[4]);
                        */
                    $em->persist($rateCard);

                }

                $i++;
            }
            $em->flush();
            $lines = $i-1;
            $this->addFlash(
                'success',
                "$lines lignes correctement ajoutées"
            );
        }

        // find all lines in rateCards
        $rateCards = $rateCards->findAll();

        return $this->render('admin/ratecard.html.twig', [
            'form' => $form->createView(),
            'rateCards' => $rateCards,
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
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/options/';
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

            // open the file to put data in DB
            $csv = fopen($destination . $newFilename, 'r');
            $i = 0;
            while (($data = fgetcsv($csv)) !== FALSE) {
                if ($i != 0) {
                    $option = new Options();
                    $option->setDescription($data[0])
                        ->setPriceOption($data[1]);
                    $em->persist($option);
                }
                $i++;
            }
            $em->flush();
            $lines = $i - 1;
            $this->addFlash(
                'success',
                "$lines lignes correctement ajoutées"
            );
        }

        // find all lines in Options
        $options = $options->findAll();

        return $this->render('admin/options.html.twig', [
            'form' => $form->createView(),
            'options' => $options,
        ]);
    }
}

