<?php

namespace App\Controller\BackEnd\Administrateur;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administrateur')]
#[IsGranted('ROLE_ADMINISTRATEUR')]
final class AdministrateurController extends AbstractController
{

    #[Route(name: 'app_accueil_administrateur_index', methods: ['GET'])]
    public function accueilAdministrateur(UtilisateurRepository $utilisateurRepository): Response
    {

        return $this->render('BackEnd/Administrateur/accueilAdministrateur.html.twig', [
            'utilisateur' => $utilisateurRepository->get_current_user(),
        ]);

    }



}
