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

#[ Route( '/administrateur' ) ]
#[ IsGranted( 'IS_AUTHENTICATED_FULLY' ) ]
#[ IsGranted( 'ROLE_EMPLOYE_ADMINISTRATEUR' ) ]
final class AdministrateurController extends AbstractController
{

    #[ Route( name: 'app_accueil_administrateur_index' , methods: [ 'GET' ] ) ]
    public function accueilAdministrateur(): Response
    {

        return $this->render( 'BackEnd/Administrateur/accueilAdministrateur.html.twig' , [
            'utilisateur' => $this->getUser() ,
        ] );

    }

    #[ Route( '/listeDesEmployes' , name: 'app_liste_des_employes' , methods: [ 'GET' ] ) ]
    public function listeDesEmployes( UtilisateurRepository $utilisateurRepository ) : Response
    {

        $listeDesEmployes = $utilisateurRepository->createQueryBuilder( 'u' )
            ->andWhere( 'u.roles LIKE :e' )
            ->setParameter( 'e' , '%EMPLOYE%' )
            ->getQuery()
            ->getResult();

        return $this->render( 'BackEnd/Administrateur/listeDesEmployes.html.twig' , [
            'listeDesEmployes' => $listeDesEmployes
        ] );

    }

}
