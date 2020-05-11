<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class functionGenerale
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     */
    public function discardDevisEmpty(User $user)
    {
        $allDevis = $user->getDevis();
        foreach ($allDevis as $devis){
            $simulations = $devis->getSimulations();
            if (count($simulations) == 0){
                $this->em->remove($devis);
                $this->em->flush();
            }
        }
    }

}