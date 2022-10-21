<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class DatabaseImportController extends AbstractController
{
    #[Route('/database_user', name: 'app_database_import_user')]
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $file = file_get_contents("visiteur.json");

        $users = json_decode(json: $file);
        //var_dump($users);

        foreach ($users as $user) {
            $newUser = new User();
            $newUser->setLogin($user->login);
            $newUser->setNom($user->nom);
            $newUser->setPrenom($user->prenom);
            $newUser->setAdresse($user->adresse);
            $newUser->setVille($user->ville);
            $newUser->setCp($user->cp);
            $newUser->setDateEmbauche(new \DateTime($user->dateEmbauche));
            $newUser->setOldId($user->id);
            $plaintextPassword = $user->mdp;
            $hashedPassword = $passwordHasher->hashPassword(
                $newUser,
                $plaintextPassword
            );
            $newUser->setPassword($hashedPassword);
            $doctrine->getManager()->persist($newUser);
            $doctrine->getManager()->flush();
        }
        return $this->render('database_import/index.html.twig', [
            'controller_name' => 'DatabaseImportController',
        ]);
    }

    #[Route('/database_fichefrais', name: 'app_database_import_fichefrais')]
    public function fichesfrais(ManagerRegistry $doctrine): Response
    {
        $fichefraisjson = file_get_contents("fichefrais.json");

        $fichesfrais = json_decode(json: $fichefraisjson);

        foreach ($fichesfrais as $fichefrais) {
            $newFicheFrais = new FicheFrais();
            $user = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $fichefrais->idVisiteur]);
            $newFicheFrais->setMois($fichefrais->mois);
            $newFicheFrais->setNbJustificatifs($fichefrais->nbJustificatifs);
            $newFicheFrais->setMontantValid($fichefrais->montantValide);
            $newFicheFrais->setDateModif(new \DateTime($fichefrais->dateModif));
            $newFicheFrais->setUser($user);

            switch ($fichefrais->idEtat) {
                case "CL";
                    $etat = $doctrine->getRepository(Etat::class)->find(1);
                    break;
                case "CR";
                    $etat = $doctrine->getRepository(Etat::class)->find(2);
                    break;
                case "RB";
                    $etat = $doctrine->getRepository(Etat::class)->find(3);
                    break;
                case "VA";
                    $etat = $doctrine->getRepository(Etat::class)->find(4);
                    break;
            }

            $newFicheFrais->setEtat($etat);

            $doctrine->getManager()->persist($newFicheFrais);
            $doctrine->getManager()->flush();

        }
        return $this->render('database_import/index.html.twig', [
            'controller_name' => 'DatabaseImportController',
        ]);
    }

    #[Route('/database_lignefraisforfait', name: 'app_database_import_lignefraisforfait')]
    public function lignesfraisforfait(ManagerRegistry $doctrine): Response
    {

        $lignesfraisforfait = file_get_contents("lignefraisforfait.json");

        $lignesfraisforfait = json_decode(json: $lignesfraisforfait);

        foreach ($lignesfraisforfait as $lignefraisforfait) {
            $newLigneFraisForfait = new LigneFraisForfait();
            $user = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $lignefraisforfait->idVisiteur]);
            $fichefrais = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => $lignefraisforfait->mois]);
            $newLigneFraisForfait->setQuantite($lignefraisforfait->quantite);
            $newLigneFraisForfait->setFicheFrais($fichefrais);

            switch ($lignefraisforfait->idFraisForfait) {
                case "ETP";
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find(1);
                    break;
                case "KM";
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find(2);
                    break;
                case "NUI";
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find(3);
                    break;
                case "REP";
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find(4);
                    break;
            }

            $newLigneFraisForfait->setFraisForfait($fraisForfait);

            $doctrine->getManager()->persist($newLigneFraisForfait);
            $doctrine->getManager()->flush();

        }
        return $this->render('database_import/index.html.twig', [
            'controller_name' => 'DatabaseImportController',
        ]);
    }

    #[Route('/database_lignefraishorsforfait', name: 'app_database_import_lignefraishorsforfait')]
    public function lignesfraishorsforfait(ManagerRegistry $doctrine): Response
    {
        $lignesfraishorsforfait = file_get_contents("lignefraishorsforfait.json");

        $lignesfraishorsforfait = json_decode(json: $lignesfraishorsforfait);

            foreach($lignesfraishorsforfait as $lignefraishorsforfait) {
                $newLigneFraisHorsForfait = new LigneFraisHorsForfait();
                $user = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $lignefraishorsforfait->idVisiteur]);
                $fichefrais = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => $lignefraishorsforfait->mois]);
                $newLigneFraisHorsForfait->setMontant($lignefraishorsforfait->montant);
                $newLigneFraisHorsForfait->setDate(new \DateTime ($lignefraishorsforfait->date));
                $newLigneFraisHorsForfait->setLibelle($lignefraishorsforfait->libelle);
                $newLigneFraisHorsForfait->setFicheFrais($fichefrais);

                $doctrine->getManager()->persist($newLigneFraisHorsForfait);
                $doctrine->getManager()->flush();
            }

        return $this->render('database_import/index.html.twig', [
        'controller_name' => 'DatabaseImportController',
        ]);
        }
}