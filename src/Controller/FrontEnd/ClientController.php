<?php

namespace App\Controller\FrontEnd;

use App\Entity\Utilisateur;
use App\Repository\ContratRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/client')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ClientController extends AbstractController {

    #[Route('/listeDesContrats/{id}' , name: 'app_liste_des_contrats', methods: 'GET')]
    public function listeDesContrats(Request $request, ContratRepository $contratRepository, UtilisateurRepository $utilisateurRepository) : Response {

        $id = $request->attributes->get( 'id' );

        $client = $utilisateurRepository->findOneBy( [ 'id' => $id ] );

        $listeDesContrats = $contratRepository->createQueryBuilder( 'c' )
        ->andWhere( 'c.idUtilisateur = :idUtilisateur' )
        ->setParameter( 'idUtilisateur' , $id )
        ->getQuery()
        ->getResult();

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        return $this->render( 'FrontEnd/listeDesContrats.html.twig' , [ 'EmployeOuAdministrateur' => $utilisateur->sesRolesContiennent( 'EMPLOYE' ), 'client' => $client, 'listeDesContrats' => $listeDesContrats ] );

    }

}