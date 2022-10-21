<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateUserController extends AbstractController
{
    #[Route('/create_user', name: 'app_create_user')]
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        // ... e.g. get the user data from a registration form
        $newUser = new User();
        $newUser->setLogin('elisabeth');
        $newUser->setNom('toto');
        $newUser->setPrenom('toto');
        $newUser->setAdresse('toto');
        $newUser->setVille('Annecy');
        $newUser->setCp('74');
        $newUser->setDateEmbauche(new \DateTime('2022-01-01'));

        $plaintextPassword = 'toto';
        $hashedPassword = $passwordHasher->hashPassword(
            $newUser,
            $plaintextPassword
        );
        $newUser->setPassword($hashedPassword);
        $doctrine->getManager()->persist($newUser);
        $doctrine->getManager()->flush();
        // actually executes the queries (i.e. the INSERT query
        return $this->render('create_user/index.html.twig', [
            'user_login' => $newUser->getLogin(),
        ]);
    }
}
