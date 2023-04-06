<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateFichesFraisController extends AbstractController
{
    #[Route('/create/fiches/frais', name: 'app_create_fiches_frais')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('create_fiches_frais/index.html.twig', [
            'controller_name' => 'CreateFichesFraisController',
        ]);
    }
}
