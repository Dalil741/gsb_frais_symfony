<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\MesFichesFraisType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFichesFraisController extends AbstractController
{
    #[Route('/mes/fiches/frais', name: 'app_mes_fiches_frais')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repository = $doctrine->getRepository(FicheFrais::class);
        $fiches = $repository->findBy(['user'=> $user]);

        foreach ($fiches as $FicheFrais){
            $listMois[$FicheFrais->getMois()] = $FicheFrais->getMois();
        }

        $myForm = $this->createForm(MesFichesFraisType::class, null, [
            'list_mois' => $listMois,
        ]);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid())
        {
           $selectMois = $myForm->get('liste_mois')->getData();
           $maFiche = $repository->findOneBy(['mois' => $selectMois, 'user' => $user]);
        }


        return $this->render('mes_fiches_frais/index.html.twig', [
            'myForm' => $myForm,
        ]);
    }
}
