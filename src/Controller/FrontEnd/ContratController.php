<?php

namespace App\Controller\FrontEnd;

use App\Entity\Utilisateur;
use App\Repository\ContratRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Form\FrontEnd\EditerUnContratType;
use App\Validation\Validations;
use App\Validation\ValidationGroups;

use Symfony\Component\Form\FormError;

#[Route('/contrat')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ContratController extends AbstractController {

    #[ Route( '/ajouterUnContrat' , name: 'app_ajouter_un_contrat' , methods: [ 'GET' , 'POST' ] ) ]
    public function ajouterUnContrat() : Response
    {
        $form = $this->createForm( EditerUnContratType::class );

        return $this->render( 'FrontEnd/EditerUnContrat.html.twig' , [ 'form' => $form,'edition' => false ] );
    }

}