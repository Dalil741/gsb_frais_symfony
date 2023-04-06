<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
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



        return $this->render('saisie_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisieFicheFraisController',

        ]);
    }
}
