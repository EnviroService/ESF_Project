<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Simulation;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/devis")
 */
class DevisController extends AbstractController
{
    /**
     * @Route("/", name="devis_index", methods={"GET"})
     * @param DevisRepository $devisRepository
     * @return Response
     */
    public function index(DevisRepository $devisRepository): Response
    {
        return $this->render('devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="devis_new")
     * @IsGranted("ROLE_USER")
     * @param Simulation $simulation
     * @param DevisRepository $deviss
     * @param EntityManagerInterface $em
     * @return Response
     */
 /*   public function new(Simulation $simulation, DevisRepository $deviss, EntityManagerInterface $em)
    {
        // on cherche si un devis existe déjà pour l'utilisateur
        $oldDevis = $deviss->findOneBy(['user' => $this->getUser()]);
        // s'il n'existe pas on le crée, sinon on l'associe au devis existant
        if(empty($oldDevis)) {
            $devis = new Devis();
            $devis->setUser($this->getUser());
            $em->persist($devis);
            $simulation->setDevis($devis);
        }
        else {
            $simulation->setDevis($oldDevis);
            $devis = $oldDevis;
        }

        $em->persist($simulation);
        $em->flush();

        return $this->redirectToRoute('devis_show', [
            'id' => $devis->getId(),
        ]);
    }*/

    /**
     * @Route("/{id}", name="devis_show", methods={"GET"})
     * @param Devis $devis
     * @return Response
     */
    public function show(Devis $devis): Response
    {
        return $this->render('devis/show.html.twig', [
            'devis' => $devis,
        ]);
    }
}
