<?php

namespace App\Controller\BackEnd;

use App\Repository\ContratRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route( '/employe' )]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class EmployeController extends AbstractController
{

    #[ Route( '/listeDesComptesClients' , name: 'app_liste_des_comptes_clients' , methods: [ 'GET' ] ) ]
    #[IsGranted('REGEX:^ROLE_EMPLOYE')]
    public function listeDesComptesClients( UtilisateurRepository $utilisateurRepository , ContratRepository $contratRepository) : Response
    {
        $listeDesComptesClients = [];

        $listeDesClients = $utilisateurRepository->createQueryBuilder( 'u' )
            ->andWhere( 'u.roles LIKE :e' )
            ->setParameter( 'e' , '%CLIENT%' )
            ->getQuery()
            ->getResult();

        foreach( $listeDesClients as $client ) {

            $listeDesContrats = $contratRepository->createQueryBuilder( 'c' )
            ->andWhere( 'c.idUtilisateur = :idUtilisateur' )
            ->setParameter( 'idUtilisateur' , $client->getId() )
            ->getQuery()
            ->getResult();

            $listeDesComptesClients[] = [ 'client' => $client , 'nbContrats' => count($listeDesContrats) ];

        }

        return $this->render( 'BackEnd/listeDesComptesClients.html.twig' , [
            'listeDesComptesClients' => $listeDesComptesClients
        ] );

    }
    
}
