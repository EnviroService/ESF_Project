<?php


namespace App\Controller;

use App\Entity\User;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use SoapClient;
use SoapFault;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiPostController extends AbstractController
{
    /**
     * @Route("/api/post/{id}", name="api_post_index", methods={"GET"})
     * @param User $user
     * @param DevisRepository $devisRepository
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws SoapFault
     */
    public function apiChronopostAe(
        User $user,
        DevisRepository $devisRepository,
        EntityManagerInterface $em
    ) {

        if ($this->getUser()->getId() == $user->getId()) {
            $wsdl = "https://ws.chronopost.fr/shipping-cxf/ShippingServiceWS?wsdl";
            $clientCh = new SoapClient($wsdl);
            //$clientCh->soap_defencoding = 'UTF-8';
            //$clientCh->decode_utf8 = false;

            $username = $user->getUsername();
            $name = $user->getBossName();

            $params = [
                //STRUCTURE HEADER VALUE
                'headerValue' => [
                    'accountNumber' => '19869502',
                    'idEmit' => 'CHRFR',
                    'identWebPro' => '',
                    'subAccount' => '',
                ],
                //STRUCTURE SHIPPERVALUE (expediteur)
                'shipperValue' => [
                    'shipperAdress1' => $user->getBillingAddress(),
                    'shipperAdress2' => '',
                    'shipperCity' => $user->getBillingCity(),
                    'shipperCivility' => 'M',
                    'shipperContactName' => "$name $username",
                    'shipperCountry' => 'FR',
                    'shipperCountryName' => 'FRANCE',
                    'shipperEmail' => $user->getEmail(),
                    'shipperMobilePhone' => '',
                    'shipperName' => $username,
                    'shipperName2' => $name,
                    'shipperPhone' => $user->getRefContact(),
                    'shipperPreAlert' => 0,
                    'shipperZipCode' => $user->getBillingPostcode(),
                ],
                //STRUCTURE CUSTOMERVALUE (client)
                'customerValue' => [
                    'customerAdress1' => '391 avenue Clément Ader',
                    'customerAdress2' => '',
                    'customerCity' => 'Wambrechies',
                    'customerCivility' => 'M',
                    'customerContactName' => 'Paul',
                    'customerCountry' => 'FR',
                    'customerCountryName' => 'FRANCE',
                    'customerEmail' => 'test@gmail.com',
                    'customerMobilePhone' => '',
                    'customerName' => 'Enviro Service France',
                    'customerName2' => '',
                    'customerPhone' => '0133333333',
                    'customerPreAlert' => 0,
                    'customerZipCode' => '59118',
                    'printAsSender' => 'N',
                ],
                //STRUCTURE RECIPIENTVALUE (destinataire)
                'recipientValue' => [
                    'recipientAdress1' => '391 avenue Clément Ader',
                    'recipientAdress2' => '',
                    'recipientCity' => 'Wambrechies',
                    'recipientContactName' => 'Paul',
                    'recipientCountry' => 'FR',
                    'recipientCountryName' => 'FRANCE',
                    'recipientEmail' => 'test@gmail.com',
                    'recipientMobilePhone' => '',
                    'recipientName' => 'Enviro Service France',
                    'recipientName2' => 'Enviro Service France',
                    'recipientPhone' => '0455667788',
                    'recipientPreAlert' => 0,
                    'recipientZipCode' => '59118',
                    'recipientCivility' => 'M',
                ],
                //STRUCTURE REFVALUE
                'refValue' => [
                    'customerSkybillNumber' => '123456789',
                    'PCardTransactionNumber' => '',
                    'recipientRef' => 24,
                    'shipperRef' => 000000000000001,
                ],
                //STRUCTURE SKYBILLVALUE
                'skybillValue' => [
                    'bulkNumber' => 1,
                    'codCurrency' => 'EUR',
                    'codValue' => 0,
                    'customsCurrency' => 'EUR',
                    'customsValue' => 0,
                    'evtCode' => 'DC',
                    'insuredCurrency' => 'EUR',
                    'insuredValue' => 0,
                    'masterSkybillNumber' => '?',
                    'objectType' => 'MAR',
                    'portCurrency' => 'EUR',
                    'portValue' => 0,
                    'productCode' => '01',
                    'service' => '0',
                    'shipDate' => date('c'),
                    'shipHour' => date('G'),
                    'skybillRank' => 1,
                    'weight' => 2,
                    'weightUnit' => 'KGM',
                    'height' => '10',
                    'length' => '30',
                    'width' => '40',
                ],

                //STRUCTURE SKYBILLPARAMSVALUE
                'skybillParamsValue' => [
                    'mode' => 'PPR',
                    'withReservation' => 0,
                ],
                //OTHERS
                'password' => '255562',
                'modeRetour' => 1,
                'numberOfParcel' => 1,
                'version' => '2.0',
                'multiparcel' => 'N'
            ];

            // YOU CAN FIND PARAMETERS YOU NEED IN HERE
            //var_dump($client_ch->__getFunctions());
            //var_dump($client_ch->__getTypes());

            try {
                //Objet StdClass

                // demande la réponse de la méthode shippingMultiParcelV2
                $results = $clientCh->shippingMultiParcelV2($params);


                //récupération de l'étiquette en base64
                $pdf = $results->return->resultMultiParcelValue->pdfEtiquette;

                // Création d'un nom de fichier pour la sauvegarde.
                $idUser = $user->getId();

                // Récupération de l'id de l'estimation passée en GET
                $devisId = $_GET['devis'];
                $date = date("d_M_Y");
                $repertory = "uploads/etiquettes/";
                $filenameSave = $repertory . "id" . $idUser . "_" . $date . "_E" . $devisId . ".pdf";
                $filename = "id" . $idUser . "_" . $date . "_E" . $devisId . ".pdf";

                if ($_GET['status'] == 2) {
                    $devis = $devisRepository->find($devisId);
                    $em->persist($devis);

                   /* $devis = $devisRepository->findOneBy([
                        'organismName' => 'Bip-Bip'
                    ]);*/

                    /*$collect = new Collects();
                    $collect->setCollector($organism)->addClient($user)->setDateCollect(new DateTime('now'));
                    $em->persist($collect);*/
                    $em->flush();
                }

                $openDir = scandir($repertory);

                if (!empty($openDir)) {
                    foreach ($openDir as $value) {
                        if ($filename === $value) {
                            $this->addFlash('danger', 'Votre étiquette a déjà été enregistrée, 
                        elle est disponible sur votre profil');
                            return $this->redirectToRoute('user_show', [
                                'id' => $idUser
                            ]);
                        }
                    }
                }

                $fichier = fopen($filenameSave, "w");
                fwrite($fichier, $pdf);
                fclose($fichier);

                return new Response($pdf, 200, [
                    'Content-Disposition' => "attachment; filename=$filename"
                ]);
            } catch (SoapFault $soapFault) {
                //var_dump($soapFault);
                echo "Request :<br>", htmlentities($clientCh->__getLastRequest()), "<br>";
                echo "Response :<br>", htmlentities($clientCh->__getLastResponse()), "<br>";
            }
        } else {
            $this->addFlash('danger', 'Vous ne pouvez pas éditer cette étiquette. Seules 
            les étiquettes vous appartenant sont disponibles');
            $id = $this->getUser()->getId();

            return $this->redirectToRoute("user_show", [
                'id' => $id
            ]);
        }
        $id = $this->getUser()->getId();

        return $this->redirectToRoute("user_show", [
            'id' => $id
        ]);
    }

}
