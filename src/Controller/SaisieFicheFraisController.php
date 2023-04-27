<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Form\NewFicheFraisLignesFraisForfaitType;
use App\Form\NewFicheFraisLignesHorsForfaitType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisieFicheFraisController extends AbstractController
{
    #[Route('/saisie/fiche/frais', name: 'app_saisie_fiche_frais')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {

        $montant = 0;
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repositoryEtat = $doctrine->getRepository(Etat::class);
        $etat = $repositoryEtat->find(['id'=> 2]);
        $user = $this->getUser();
        $moisencours = date('Ym');

        $repositoryFicheFrais = $doctrine->getRepository(FicheFrais::class);
        $fiche = $repositoryFicheFrais->findOneBy(['user'=> $user, 'mois' => $moisencours]);


        if($fiche == null){
            $fiche = new FicheFrais();
            $repositoryFraisForfait = $doctrine->getRepository(FraisForfait::class);

            $forfaitEtape = $repositoryFraisForfait->find(1);
            $fraisKilometrique = $repositoryFraisForfait->find(2);
            $nuiteeHotel = $repositoryFraisForfait->find(3);
            $repasRestaurant = $repositoryFraisForfait->find(4);

            $lffForfaitEtape = new LigneFraisForfait();
            $lffForfaitEtape->setFraisForfait($forfaitEtape);
            $lffForfaitEtape->setQuantite(0);

            $lffFraisKilometrique = new LigneFraisForfait();
            $lffFraisKilometrique->setFraisForfait($fraisKilometrique);
            $lffFraisKilometrique->setQuantite(0);

            $lffNuiteeHotel = new LigneFraisForfait();
            $lffNuiteeHotel->setFraisForfait($nuiteeHotel);
            $lffNuiteeHotel->setQuantite(0);

            $lffRepasRestaurant = new LigneFraisForfait();
            $lffRepasRestaurant->setFraisForfait($repasRestaurant);
            $lffRepasRestaurant->setQuantite(0);

            $fiche->setUser($user);
            $fiche->setEtat($etat);
            $fiche->setMontantValid(0);
            $fiche->setDateModif(new \DateTime());
            $fiche->setMois($moisencours);
            $fiche->setNbJustificatifs(0);
            $fiche->addLigneFraisForfait($lffForfaitEtape);
            $fiche->addLigneFraisForfait($lffFraisKilometrique);
            $fiche->addLigneFraisForfait($lffNuiteeHotel);
            $fiche->addLigneFraisForfait($lffRepasRestaurant);

            $doctrine->getManager()->persist($fiche);
            $doctrine->getManager()->flush();
        }

        $formLignesFrais = $this->createForm(NewFicheFraisLignesFraisForfaitType::class, null, ['fiche' => $fiche] );
        $formLignesFrais->handleRequest($request);
        if ($formLignesFrais->isSubmitted() && $formLignesFrais->isValid()) {
            $fiche->getLigneFraisForfaits()[0]->setQuantite($formLignesFrais->get('ForfaitEtape')->getData()) ;
            $fiche->getLigneFraisForfaits()[1]->setQuantite($formLignesFrais->get('FraisKilometrique')->getData()) ;
            $fiche->getLigneFraisForfaits()[2]->setQuantite($formLignesFrais->get('NuiteeHotel')->getData()) ;
            $fiche->getLigneFraisForfaits()[3]->setQuantite($formLignesFrais->get('RepasRestaurant')->getData()) ;


            $doctrine->getManager()->persist($fiche);
            $doctrine->getManager()->flush();

        }

            $LignesFraisHF = new LigneFraisHorsForfait();
            $formLignesFraisHF = $this->createForm(NewFicheFraisLignesHorsForfaitType::class, $LignesFraisHF);
            $formLignesFraisHF->handleRequest($request);
            if ($formLignesFraisHF->isSubmitted() && $formLignesFraisHF->isValid()) {
                $fiche->addLigneFraisHorsForfait($LignesFraisHF);

                $doctrine->getManager()->persist($fiche);
                $doctrine->getManager()->flush();
            }



        return $this->render('saisie_fiche_frais/index.html.twig', [
            'formLignesFrais' => $formLignesFrais->createView(),
            'formLignesFraisHF' => $formLignesFraisHF->createView(),
            'fiche'=> $fiche,

        ]);
    }
}
