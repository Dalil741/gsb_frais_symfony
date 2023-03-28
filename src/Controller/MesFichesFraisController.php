<?php

namespace App\Controller;

use App\Entity\FicheFrais;
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
            $mois[] = $FicheFrais->getMois();
        }
        $myForm = $this->createForm(ChoiceType::class);
        $myForm->handleRequest($request);

        $MaFiche = [];
        if ($myForm->isSubmitted() && $myForm->isValid())
        {

        }

        return $this->render('mes_fiches_frais/index.html.twig', [
            'controller_name' => 'MesFichesFraisController',
        ]);
    }
}
